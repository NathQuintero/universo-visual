@extends('layouts.app')
@section('title', 'Editar ' . $work->tracking_code)

@section('content')
    <div class="ph">
        <h2>✏️ Editar: {{ $work->tracking_code }}</h2>
        <div class="ph-acts">
            <a href="{{ route('works.show', $work) }}" class="btn btn-sm btn-s">← Volver al detalle</a>
        </div>
    </div>

    {{-- Info del cliente (no editable) --}}
    <div style="background:var(--bg-card);border:1px solid var(--border);border-radius:var(--r-md);padding:14px 18px;margin-bottom:18px;display:flex;align-items:center;gap:12px">
        <span style="font-size:20px">👤</span>
        <div>
            <strong>{{ $work->client->full_name }}</strong>
            <span style="color:var(--text-muted);font-size:12px;margin-left:8px">CC {{ $work->client->document_number }}</span>
        </div>
        <span class="badge badge-{{ $work->status_color }}" style="margin-left:auto">{{ $work->status_emoji }} {{ $work->status_name }}</span>
    </div>

    <form action="{{ route('works.update', $work) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="card" style="margin-bottom:20px">
            <div class="card-b">
                {{-- MONTURA Y LENTE --}}
                <div class="fsec-title">👓 Montura y Lente</div>
                <div class="frow">
                    <div class="fg">
                        <label>Montura *</label>
                        <select name="frame_type" required>
                            <option value="own" {{ old('frame_type', $work->frame_type) == 'own' ? 'selected' : '' }}>Propia del cliente</option>
                            <option value="purchased" {{ old('frame_type', $work->frame_type) == 'purchased' ? 'selected' : '' }}>Comprada en óptica</option>
                        </select>
                    </div>
                    <div class="fg">
                        <label>Marca / Referencia</label>
                        <input name="frame_brand" value="{{ old('frame_brand', $work->frame_brand) }}" placeholder="Ray-Ban RB5154">
                    </div>
                </div>

                <div class="frow3">
                    <div class="fg">
                        <label>Tipo de Lente *</label>
                        <select name="lens_type" required>
                            <option value="monofocal" {{ old('lens_type', $work->lens_type) == 'monofocal' ? 'selected' : '' }}>Monofocal</option>
                            <option value="bifocal" {{ old('lens_type', $work->lens_type) == 'bifocal' ? 'selected' : '' }}>Bifocal</option>
                            <option value="progressive" {{ old('lens_type', $work->lens_type) == 'progressive' ? 'selected' : '' }}>Progresivo</option>
                        </select>
                    </div>
                    <div class="fg">
                        <label>Material *</label>
                        <select name="lens_material" required>
                            <option value="cr39" {{ old('lens_material', $work->lens_material) == 'cr39' ? 'selected' : '' }}>CR-39</option>
                            <option value="polycarbonate" {{ old('lens_material', $work->lens_material) == 'polycarbonate' ? 'selected' : '' }}>Policarbonato</option>
                            <option value="high_index" {{ old('lens_material', $work->lens_material) == 'high_index' ? 'selected' : '' }}>Alto Índice</option>
                            <option value="trivex" {{ old('lens_material', $work->lens_material) == 'trivex' ? 'selected' : '' }}>Trivex</option>
                        </select>
                    </div>
                    <div class="fg">
                        <label>Laboratorio *</label>
                        <select name="laboratory_id" required>
                            @foreach($laboratories as $lab)
                                <option value="{{ $lab->id }}" {{ old('laboratory_id', $work->laboratory_id) == $lab->id ? 'selected' : '' }}>
                                    {{ $lab->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                {{-- TRATAMIENTOS --}}
                <div class="fg">
                    <label>Tratamientos</label>
                    <div style="display:flex;gap:8px;flex-wrap:wrap;margin-top:5px">
                        <label style="display:flex;align-items:center;gap:5px;background:var(--bg-input);padding:7px 12px;border-radius:var(--r);font-size:13px;cursor:pointer;border:1px solid var(--border)">
                            <input type="checkbox" name="treatment_antireflective" value="1" {{ old('treatment_antireflective', $work->treatment_antireflective) ? 'checked' : '' }}> Antirreflejo
                        </label>
                        <label style="display:flex;align-items:center;gap:5px;background:var(--bg-input);padding:7px 12px;border-radius:var(--r);font-size:13px;cursor:pointer;border:1px solid var(--border)">
                            <input type="checkbox" name="treatment_photochromic" value="1" {{ old('treatment_photochromic', $work->treatment_photochromic) ? 'checked' : '' }}> Fotocromático
                        </label>
                        <label style="display:flex;align-items:center;gap:5px;background:var(--bg-input);padding:7px 12px;border-radius:var(--r);font-size:13px;cursor:pointer;border:1px solid var(--border)">
                            <input type="checkbox" name="treatment_blue_filter" value="1" {{ old('treatment_blue_filter', $work->treatment_blue_filter) ? 'checked' : '' }}> Filtro Azul
                        </label>
                        <label style="display:flex;align-items:center;gap:5px;background:var(--bg-input);padding:7px 12px;border-radius:var(--r);font-size:13px;cursor:pointer;border:1px solid var(--border)">
                            <input type="checkbox" name="treatment_polarized" value="1" {{ old('treatment_polarized', $work->treatment_polarized) ? 'checked' : '' }}> Polarizado
                        </label>
                    </div>
                </div>

                {{-- PRECIOS --}}
                <div class="fsec-title">💰 Precios</div>

                {{-- Aviso de pagos existentes --}}
                @if($work->payments->count() > 0)
                    <div style="background:rgba(255,193,7,0.06);border:1px solid rgba(255,193,7,0.15);border-radius:var(--r);padding:10px 14px;margin-bottom:14px;font-size:12px;color:var(--yellow)">
                        ⚠️ Este trabajo tiene {{ $work->payments->count() }} abono(s) por ${{ number_format($work->total_paid, 0, ',', '.') }}. 
                        Si cambias los precios, el saldo se recalculará automáticamente.
                    </div>
                @endif

                <div class="frow3">
                    <div class="fg">
                        <label>Lentes ($) *</label>
                        <input name="price_lenses" type="number" step="1" value="{{ old('price_lenses', $work->price_lenses) }}" required>
                    </div>
                    <div class="fg">
                        <label>Montura ($)</label>
                        <input name="price_frame" type="number" step="1" value="{{ old('price_frame', $work->price_frame) }}">
                    </div>
                    <div class="fg">
                        <label>Consulta ($)</label>
                        <input name="price_consultation" type="number" step="1" value="{{ old('price_consultation', $work->price_consultation) }}">
                    </div>
                </div>

                {{-- ETIQUETAS --}}
                <div class="fsec-title">⭐ Etiquetas</div>
                <div style="display:flex;gap:8px;margin-bottom:16px">
                    <label style="display:flex;align-items:center;gap:5px;background:rgba(239,68,68,0.06);padding:7px 12px;border-radius:var(--r);font-size:13px;cursor:pointer;border:1px solid rgba(239,68,68,0.15);color:var(--red)">
                        <input type="checkbox" name="is_urgent" value="1" {{ old('is_urgent', $work->is_urgent) ? 'checked' : '' }}> 🔥 Urgente
                    </label>
                    <label style="display:flex;align-items:center;gap:5px;background:rgba(255,193,7,0.06);padding:7px 12px;border-radius:var(--r);font-size:13px;cursor:pointer;border:1px solid rgba(255,193,7,0.15);color:var(--yellow)">
                        <input type="checkbox" name="is_vip" value="1" {{ old('is_vip', $work->is_vip) ? 'checked' : '' }}> ⭐ VIP
                    </label>
                    <label style="display:flex;align-items:center;gap:5px;background:rgba(124,91,245,0.06);padding:7px 12px;border-radius:var(--r);font-size:13px;cursor:pointer;border:1px solid rgba(124,91,245,0.15);color:var(--purple)">
                        <input type="checkbox" name="is_warranty" value="1" {{ old('is_warranty', $work->is_warranty) ? 'checked' : '' }}> 🔄 Garantía
                    </label>
                </div>

                <div class="fg">
                    <label>Fecha estimada de entrega</label>
                    <input name="estimated_delivery" type="date" value="{{ old('estimated_delivery', $work->estimated_delivery?->format('Y-m-d')) }}" style="max-width:220px">
                </div>

                {{-- OBSERVACIONES --}}
                <div class="fsec-title">📝 Observaciones</div>
                <div class="fg">
                    <textarea name="observations" rows="3" 
                              style="resize:vertical;width:100%;background:var(--bg-input);border:1px solid var(--border);border-radius:var(--r);padding:10px 13px;color:var(--text-primary);font-family:'Outfit';font-size:13.5px">{{ old('observations', $work->observations) }}</textarea>
                </div>

                {{-- BOTONES --}}
                <div style="display:flex;gap:10px;justify-content:flex-end;margin-top:18px;padding-top:18px;border-top:1px solid var(--border)">
                    <a href="{{ route('works.show', $work) }}" class="btn btn-s">Cancelar</a>
                    <button type="submit" class="btn btn-p">💾 Guardar Cambios</button>
                </div>
            </div>
        </div>
    </form>
@endsection