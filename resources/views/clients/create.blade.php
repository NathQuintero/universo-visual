@extends('layouts.app')
@section('title', 'Nuevo Cliente')

@section('content')
    <div class="ph">
        <h2>➕ Nuevo Cliente</h2>
        <div class="ph-acts">
            <a href="{{ route('clients.index') }}" class="btn btn-sm btn-s">← Volver</a>
        </div>
    </div>

    <form action="{{ route('clients.store') }}" method="POST">
        @csrf
        <div class="card">
            <div class="card-b">
                <div class="fsec-title">👤 Datos Personales</div>
                <div class="frow">
                    <div class="fg">
                        <label>Nombre *</label>
                        <input name="first_name" value="{{ old('first_name') }}" placeholder="María" required>
                        @error('first_name') <span class="field-error">{{ $message }}</span> @enderror
                    </div>
                    <div class="fg">
                        <label>Apellidos *</label>
                        <input name="last_name" value="{{ old('last_name') }}" placeholder="López González" required>
                        @error('last_name') <span class="field-error">{{ $message }}</span> @enderror
                    </div>
                </div>
                <div class="frow">
                    <div class="fg">
                        <label>Tipo de Documento</label>
                        <select name="document_type">
                            <option value="CC" {{ old('document_type') == 'CC' ? 'selected' : '' }}>CC — Cédula de Ciudadanía</option>
                            <option value="TI" {{ old('document_type') == 'TI' ? 'selected' : '' }}>TI — Tarjeta de Identidad</option>
                            <option value="CE" {{ old('document_type') == 'CE' ? 'selected' : '' }}>CE — Cédula de Extranjería</option>
                            <option value="PA" {{ old('document_type') == 'PA' ? 'selected' : '' }}>PA — Pasaporte</option>
                        </select>
                    </div>
                    <div class="fg">
                        <label>Número de Documento *</label>
                        <input name="document_number" value="{{ old('document_number') }}" placeholder="63524891" required>
                        @error('document_number') <span class="field-error">{{ $message }}</span> @enderror
                    </div>
                </div>

                <div class="fsec-title">📞 Contacto</div>
                <div class="frow">
                    <div class="fg">
                        <label>Teléfono / WhatsApp</label>
                        <input name="phone" value="{{ old('phone') }}" placeholder="3151234567">
                    </div>
                    <div class="fg">
                        <label>Email</label>
                        <input name="email" type="email" value="{{ old('email') }}" placeholder="maria@email.com">
                    </div>
                </div>
                <div class="fg">
                    <label>Dirección</label>
                    <input name="address" value="{{ old('address') }}" placeholder="Cra 25 #45-12, Cabecera, Bucaramanga">
                </div>
                <div class="frow">
                    <div class="fg">
                        <label>Fecha de Nacimiento</label>
                        <input name="birth_date" type="date" value="{{ old('birth_date') }}">
                    </div>
                    <div class="fg">
                        <label style="margin-bottom:8px">WhatsApp</label>
                        <label style="display:flex;align-items:center;gap:6px;font-size:13px;cursor:pointer">
                            <input type="checkbox" name="whatsapp_authorized" value="1" {{ old('whatsapp_authorized') ? 'checked' : '' }}>
                            ✅ Autoriza recibir mensajes por WhatsApp
                        </label>
                    </div>
                </div>

                <div class="fsec-title">📝 Notas</div>
                <div class="fg">
                    <textarea name="notes" rows="2" style="resize:vertical;width:100%;background:var(--bg-input);border:1px solid var(--border);border-radius:var(--r);padding:10px 13px;color:var(--text-primary);font-family:'Outfit';font-size:13.5px" placeholder="Observaciones sobre el cliente...">{{ old('notes') }}</textarea>
                </div>

                <div style="display:flex;gap:10px;justify-content:flex-end;margin-top:18px;padding-top:18px;border-top:1px solid var(--border)">
                    <a href="{{ route('clients.index') }}" class="btn btn-s">Cancelar</a>
                    <button type="submit" class="btn btn-p">💾 Guardar Cliente</button>
                </div>
            </div>
        </div>
    </form>
@endsection