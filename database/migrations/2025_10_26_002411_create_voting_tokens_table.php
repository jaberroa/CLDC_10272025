<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('voting_tokens', function (Blueprint $table) {
            $table->uuid('jti')->primary()->comment('JWT ID único');
            $table->string('voter_hash', 64)->comment('Hash del votante (anonimizado)');
            $table->foreignId('eleccion_id')->constrained('elecciones')->onDelete('cascade');
            $table->string('token_signature', 255)->comment('Firma HMAC del token');
            $table->boolean('used')->default(false)->index()->comment('Token ya usado');
            $table->timestamp('issued_at')->comment('Fecha de emisión');
            $table->timestamp('expires_at')->index()->comment('Fecha de expiración');
            $table->timestamp('used_at')->nullable()->comment('Fecha de uso');
            $table->ipAddress('used_from_ip')->nullable();
            $table->string('user_agent')->nullable();
            $table->foreignId('voto_id')->nullable()->constrained('votos')->onDelete('set null');
            
            // Índices para rendimiento
            $table->index(['voter_hash', 'eleccion_id']);
            $table->index(['used', 'expires_at']);
            $table->index('issued_at');
        });

        // Tabla de rate limiting por IP
        Schema::create('voting_rate_limits', function (Blueprint $table) {
            $table->id();
            $table->ipAddress('ip_address')->index();
            $table->string('action', 50); // 'token_request', 'vote_submit'
            $table->timestamp('attempted_at')->index();
            $table->boolean('success')->default(false);
            
            $table->index(['ip_address', 'action', 'attempted_at']);
        });

        // Tabla de claves de firma rotativas
        Schema::create('signing_keys', function (Blueprint $table) {
            $table->id();
            $table->string('key_id', 50)->unique();
            $table->text('key_value')->comment('Clave encriptada');
            $table->boolean('active')->default(true)->index();
            $table->timestamp('created_at');
            $table->timestamp('rotated_at')->nullable();
            $table->timestamp('expires_at')->nullable();
            
            $table->index(['active', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('voting_rate_limits');
        Schema::dropIfExists('voting_tokens');
        Schema::dropIfExists('signing_keys');
    }
};
