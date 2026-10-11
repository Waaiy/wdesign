<div class="pick" data-color="{{ $name }}">
    <div class="pick__row">
        <span class="pick__preview" data-preview aria-hidden="true"></span>
        <input type="color" class="pick__native" data-native value="#000000" aria-label="{{ $label }} seçici">
        <input type="text" class="pick__hex" data-hex value="#000000" maxlength="7" spellcheck="false" inputmode="text" aria-label="{{ $label }} hex kodu">
    </div>
    <div class="pick__sliders">
        <label class="pick__slider">
            <span class="pick__ch">R</span>
            <input type="range" min="0" max="255" step="1" data-channel="r" aria-label="{{ $label }} kırmızı kanalı">
            <output data-out="r">0</output>
        </label>
        <label class="pick__slider">
            <span class="pick__ch">G</span>
            <input type="range" min="0" max="255" step="1" data-channel="g" aria-label="{{ $label }} yeşil kanalı">
            <output data-out="g">0</output>
        </label>
        <label class="pick__slider">
            <span class="pick__ch">B</span>
            <input type="range" min="0" max="255" step="1" data-channel="b" aria-label="{{ $label }} mavi kanalı">
            <output data-out="b">0</output>
        </label>
    </div>
</div>
