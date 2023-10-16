    <div class="form-group">
        <label for="{{ $groupLabel[0]->nama }}" class="col-sm-3 control-label">{{ $label }}</label>
        <div class="col-sm-9 row">
        @foreach($groupLabel as $item)
            @php
                $nama = isset($keyname) ? underscore($item->nama, true, true).'_'.$keyname : underscore($item->nama, true,
                true);
                $class = buat_class($item->atribut, '', $item->required);
                $widthClass = $item->kolom ? 'col-sm-'.$item->kolom : 'col-sm-12';
            @endphp
            @if ($item->tipe == 'select-manual')
            <div class="{{ $widthClass }}">
                <select name="{{ $nama }}" {!! $class !!}>
                    <option value="">-- {{ $item->deskripsi }} --</option>
                    @foreach ($item->pilihan as $key => $pilih)
                    <option @selected(set_value($nama)==$pilih) value="{{ $pilih }}">{{ $pilih }}</option>
                    @endforeach
                </select>
            </div>
            @elseif ($item->tipe == 'select-otomatis')
            <div class="{{ $widthClass }}">
                <select name="{{ $nama }}" {!! $class !!} placeholder="{{ $item->deskripsi }}">
                    <option value="">-- {{ $item->deskripsi }} --</option>
                    @foreach (ref($item->refrensi) as $key => $pilih)
                    <option @selected(set_value($nama)==$pilih->nama) value="{{ $pilih->nama }}">{{ $pilih->nama }}
                    </option>
                    @endforeach
                </select>
            </div>
            @elseif ($item->tipe == 'textarea')
            <div class="{{ $widthClass }}">
                <textarea name="{{ $nama }}" {!! $class !!}
                    placeholder="{{ $item->deskripsi }}">{{ set_value($nama) }}</textarea>
            </div>
            @elseif ($item->tipe == 'date' || $item->tipe == 'hari' || $item->tipe == 'hari-tanggal')
            <div class="{{ $widthClass }}">
                <div class="input-group input-group-sm date">
                    <div class="input-group-addon">
                        <i class="fa fa-calendar"></i>
                    </div>
                    <input type="text" {!! buat_class($item->atribut, 'form-control input-sm tgl', $item->required) !!}
                        name="{{ $nama }}" placeholder="{{ $item->deskripsi }}" value="{{ set_value($nama) }}" />
                </div>
            </div>
            @elseif ($item->tipe == 'time')
            <div class="{{ $widthClass }}">
                <div class="input-group input-group-sm date">
                    <div class="input-group-addon">
                        <i class="fa fa-clock-o"></i>
                    </div>
                    <input type="text" {!! buat_class($item->atribut, 'form-control input-sm jam', $item->required) !!}
                        name="{{ $nama }}" placeholder="{{ $item->deskripsi }}" value="{{ set_value($nama) }}" />
                </div>
            </div>
            @elseif ($item->tipe == 'datetime')
            <div class="{{ $widthClass }}">
                <div class="input-group input-group-sm date">
                    <div class="input-group-addon">
                        <i class="fa fa-calendar"></i>
                    </div>
                    <input type="text" {!! buat_class($item->atribut, 'form-control input-sm tgl_jam', $item->required) !!}
                        name="{{ $nama }}" placeholder="{{ $item->deskripsi }}" value="{{ set_value($nama) }}" />
                </div>
            </div>
            @else
            <div class="{{ $widthClass }}">
                <input type="{{ $item->tipe }}" {!! $class !!} name="{{ $nama }}" placeholder="{{ $item->deskripsi }}"
                    value="{{ set_value($nama) }}" />
            </div>
            @endif        
        @endforeach
        </div>
    </div>