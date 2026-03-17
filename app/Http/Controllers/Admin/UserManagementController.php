<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRolesRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Maatwebsite\Excel\Facades\Excel;
use Spatie\Permission\Models\Role;

class UserManagementController extends Controller
{
    public function index(Request $request): View
    {
        $roles = Role::query()->orderBy('name')->pluck('name');

        $users = User::query()
            ->with('roles')
            ->when($request->filled('q'), function ($query) use ($request): void {
                $search = trim((string) $request->string('q'));
                $query->where(function ($sub) use ($search): void {
                    $sub->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%")
                        ->orWhere('document_number', 'like', "%{$search}%");
                });
            })
            ->when($request->filled('role'), fn ($q) => $q->role($request->string('role')->toString()))
            ->when($request->filled('status'), fn ($q) => $q->where('status', $request->string('status')->toString()))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('admin.users.index', [
            'users' => $users,
            'roles' => $roles,
        ]);
    }

    public function store(StoreUserRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $roles = $data['roles'];
        unset($data['roles'], $data['password_confirmation']);

        $user = User::query()->create([
            ...$data,
            'password' => Hash::make($data['password']),
            'is_external' => in_array('Asesor', $roles, true),
            'status' => 'active',
        ]);

        $user->syncRoles($roles);

        return back()->with('success', 'Usuario creado correctamente.');
    }

    public function updateRoles(UpdateUserRolesRequest $request, User $user): RedirectResponse
    {
        $payload = $request->validated();
        $user->syncRoles($payload['roles']);

        $updateData = [
            'email' => $payload['email'],
            'status' => $payload['status'] ?? $user->status,
            'is_external' => in_array('Asesor', $payload['roles'], true),
        ];

        if (! empty($payload['password'])) {
            $updateData['password'] = Hash::make($payload['password']);
        }

        $user->update($updateData);

        return back()->with('success', 'Usuario actualizado correctamente.');
    }

    public function bulkStore(): RedirectResponse
    {
        @set_time_limit(0);
        @ini_set('max_execution_time', '0');

        $data = request()->validate([
            'archivo' => ['required', 'file', 'mimes:xlsx,xls,csv', 'max:20480'],
            'default_role' => ['nullable', 'string'],
        ]);

        $sheets = Excel::toArray([], $data['archivo']);
        $rows = $sheets[0] ?? [];
        if (count($rows) < 2) {
            return back()->with('error', 'El archivo no contiene filas para importar.');
        }

        $headers = array_map(fn ($h) => Str::of((string) $h)->ascii()->lower()->replaceMatches('/[^a-z0-9]+/', '_')->trim('_')->toString(), $rows[0]);
        $created = 0;
        $updated = 0;
        $skipped = 0;

        foreach (array_slice($rows, 1) as $row) {
            $record = [];
            foreach ($headers as $idx => $key) {
                if ($key !== '') {
                    $record[$key] = $row[$idx] ?? null;
                }
            }

            $email = trim((string) ($record['email'] ?? ''));
            $name = trim((string) ($record['name'] ?? $record['nombre'] ?? ''));
            $doc = trim((string) ($record['document_number'] ?? $record['numero_documento'] ?? ''));

            if ($email === '' || $doc === '') {
                continue;
            }

            if ($name === '') {
                $name = (string) Str::of($email)->before('@');
                if (trim($name) === '') {
                    $name = 'Usuario '.$doc;
                }
            }

            $passwordRaw = trim((string) ($record['password'] ?? ''));

            $existingByDoc = User::query()->where('document_number', $doc)->first();
            $existingByEmail = User::query()->where('email', $email)->first();

            $user = $existingByDoc
                ?? $existingByEmail
                ?? new User();

            $isNew = ! $user->exists;

            if ($existingByDoc && $existingByEmail && $existingByDoc->id !== $existingByEmail->id) {
                $skipped++;
                continue;
            }

            $user->name = $name;
            $user->document_type = (string) ($record['document_type'] ?? $record['tipo_documento'] ?? 'CC');
            $user->document_number = $doc;
            $user->phone = (string) ($record['phone'] ?? $record['telefono'] ?? '');
            $user->city = (string) ($record['city'] ?? $record['ciudad'] ?? '');
            $user->status = (string) ($record['status'] ?? 'active');

            if ($email !== '') {
                $emailTakenByOther = User::query()
                    ->where('email', $email)
                    ->when($user->exists, fn ($q) => $q->where('id', '!=', $user->id))
                    ->exists();

                if (! $emailTakenByOther) {
                    $user->email = $email;
                }
            }

            if ($passwordRaw !== '') {
                $user->password = Hash::make($passwordRaw, ['rounds' => 8]);
            } elseif ($isNew) {
                $generatedPassword = substr($doc, -6) ?: 'Cambiar123*';
                $user->password = Hash::make($generatedPassword, ['rounds' => 8]);
            }

            $user->save();

            $roles = collect(explode(',', (string) ($record['roles'] ?? '')))
                ->map(fn ($r) => trim($r))
                ->filter()
                ->values();

            if ($roles->isEmpty() && ! empty($data['default_role'])) {
                $roles = collect([$data['default_role']]);
            }

            if ($roles->isNotEmpty()) {
                $validRoles = Role::query()->whereIn('name', $roles->all())->pluck('name')->all();
                if (! empty($validRoles)) {
                    $user->syncRoles($validRoles);
                }
            }

            $user->update(['is_external' => $user->hasRole('Asesor')]);

            $isNew ? $created++ : $updated++;
        }

        return back()->with('success', "Carga masiva finalizada. Creados: {$created}, actualizados: {$updated}, omitidos por conflicto: {$skipped}.");
    }

    public function downloadTemplate(): Response
    {
        $csv = implode(',', [
            'name',
            'email',
            'document_type',
            'document_number',
            'phone',
            'city',
            'roles',
            'password',
            'status',
        ])."\n";

        $csv .= implode(',', [
            'Juan Perez',
            'juan.perez@empresa.com',
            'CC',
            '123456789',
            '3001234567',
            'Bogota',
            '"Asesor"',
            'Temporal123*',
            'active',
        ])."\n";

        $csv .= implode(',', [
            '',
            'sin.nombre@empresa.com',
            'CC',
            '987654321',
            '',
            '',
            '"Gestor PQRSF"',
            '',
            'active',
        ])."\n";

        return response($csv, 200, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="plantilla_carga_usuarios.csv"',
        ]);
    }
}
