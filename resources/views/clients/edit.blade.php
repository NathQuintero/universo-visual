@extends('layouts.app')
@section('title', 'Editar Cliente')

@section('content')
    <div class="ph">
        <h2>✏️ Editar: {{ $client->full_name }}</h2>
        <div class="ph-acts">
            <a href="{{ route('clients.index') }}" class="btn btn-sm btn-s">← Volver</a>
        </div>
    </div>

    <form action="{{ route('clients.update', $client) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="card">
            <div class="card-b">
                <div class="fsec-title">👤 Datos Personales</div>
                <div class="frow">
                    <div class="fg">
                        <label>Nombre *</label>
                        <input name="first_name" value="{{ old('first_name', $client->first_name) }}" required>
                    </div>
                    <div class="fg">
                        <label>Apellidos *</label>
                        <input name="last_name" value="{{ old('last_name', $client->last_name) }}" required>
                    </div>
                </div>
                <div class="frow">
                    <div class="fg">
                        <label>Tipo de Documento</label>
                        <select name="document_type">
                            @foreach(['CC' => 'Cédula', 'TI' => 'Tarjeta Identidad', 'CE' => 'Cédula Extranjería', 'PA' => 'Pasaporte'] as $val => $label)
                                <option value="{{ $val }}" {{ old('document_type', $client->document_type) == $val ? 'selected' : '' }}>{{ $val }} — {{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="fg">
                        <label>Número de Documento *</label>
                        <input name="document_number" value="{{ old('document_number', $client->document_number) }}" required>
                        @error('document_number') <span class="field-error">{{ $message }}</span> @enderror
                    </div>
                </div>

                <div class="fsec-title">📞 Contacto</div>
                <div class="frow">
                    <div class="fg"><label>Teléfono</label><input name="phone" value="{{ old('phone', $client->phone) }}"></div>
                    <div class="fg"><label>Email</label><input name="email" type="email" value="{{ old('email', $client->email) }}"></div>
                </div>
                <div class="fg"><label>Dirección</label><input name="address" value="{{ old('address', $client->address) }}"></div>
                <div class="frow">
                    <div class="fg"><label>Fecha de Nacimiento</label><input name="birth_date" type="date" value="{{ old('birth_date', $client->birth_date?->format('Y-m-d')) }}"></div>
                    <div class="fg">
                        <label style="margin-bottom:8px">WhatsApp</label>
                        <label style="display:flex;align-items:center;gap:6px;font-size:13px;cursor:pointer">
                            <input type="checkbox" name="whatsapp_authorized" value="1" {{ old('whatsapp_authorized', $client->whatsapp_authorized) ? 'checked' : '' }}>
                            ✅ Autoriza mensajes por WhatsApp
                        </label>
                    </div>
                </div>

                <div class="fsec-title">📝 Notas</div>
                <div class="fg">
                    <textarea name="notes" rows="2" style="resize:vertical;width:100%;background:var(--bg-input);border:1px solid var(--border);border-radius:var(--r);padding:10px 13px;color:var(--text-primary);font-family:'Outfit';font-size:13.5px">{{ old('notes', $client->notes) }}</textarea>
                </div>

                <div style="display:flex;gap:10px;justify-content:flex-end;margin-top:18px;padding-top:18px;border-top:1px solid var(--border)">
                    <a href="{{ route('clients.index') }}" class="btn btn-s">Cancelar</a>
                    <button type="submit" class="btn btn-p">💾 Guardar Cambios</button>
                </div>
            </div>
        </div>
    </form>
@endsection