<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Pqrsf extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'pqrsf';

    protected $fillable = [
        'radicado',
        'pqrsf_tipo_id',
        'destinatario_id',
        'destinatario_original_id',
        'pqrsf_estado_id',
        'prioridad',
        'canal_ingreso',
        'nombres',
        'apellidos',
        'tipo_documento',
        'numero_documento',
        'email',
        'telefono',
        'ciudad',
        'asunto',
        'descripcion',
        'acepta_tratamiento_datos',
        'asignado_a',
        'creado_por',
        'fecha_limite_respuesta',
        'fecha_respuesta',
        'fecha_cierre',
        'vencida',
        'respuesta_final',
        'meta',
    ];

    protected $appends = ['dias_transcurridos', 'dias_restantes', 'proxima_vencer'];

    protected function casts(): array
    {
        return [
            'acepta_tratamiento_datos' => 'boolean',
            'fecha_limite_respuesta' => 'datetime',
            'fecha_respuesta' => 'datetime',
            'fecha_cierre' => 'datetime',
            'vencida' => 'boolean',
            'meta' => 'array',
        ];
    }

    public function tipo(): BelongsTo
    {
        return $this->belongsTo(PqrsfTipo::class, 'pqrsf_tipo_id');
    }

    public function estado(): BelongsTo
    {
        return $this->belongsTo(PqrsfEstado::class, 'pqrsf_estado_id');
    }

    public function destinatario(): BelongsTo
    {
        return $this->belongsTo(PqrsfDestinatario::class, 'destinatario_id');
    }

    public function destinatarioOriginal(): BelongsTo
    {
        return $this->belongsTo(PqrsfDestinatario::class, 'destinatario_original_id');
    }

    public function asignadoA(): BelongsTo
    {
        return $this->belongsTo(User::class, 'asignado_a');
    }

    public function historial(): HasMany
    {
        return $this->hasMany(PqrsfHistorial::class);
    }

    public function adjuntos(): HasMany
    {
        return $this->hasMany(PqrsfAdjunto::class);
    }

    public function respuestas(): HasMany
    {
        return $this->hasMany(PqrsfRespuesta::class);
    }

    public function getDiasTranscurridosAttribute(): ?int
    {
        return $this->created_at ? Carbon::parse($this->created_at)->diffInDays(now()) : null;
    }

    public function getDiasRestantesAttribute(): ?int
    {
        return $this->fecha_limite_respuesta ? now()->diffInDays($this->fecha_limite_respuesta, false) : null;
    }

    public function getProximaVencerAttribute(): bool
    {
        return $this->dias_restantes !== null && $this->dias_restantes >= 0 && $this->dias_restantes <= 2;
    }
}
