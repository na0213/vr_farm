{{-- てくてく歩く動物たち(牛・豚・卵・ひよこ)。飾りなので読み上げない。色と動きは farm360.css の .walkers --}}
<div class="walkers" aria-hidden="true">
    {{-- 牛 --}}
    <div class="walker cow" style="--walk-delay: -25s">
        <svg viewBox="0 0 80 64" focusable="false">
            <g class="legs">
                <path class="leg a" d="M17 49 V60 M14.5 60.5 H20"/>
                <path class="leg b" d="M25 50 V60 M22.5 60.5 H28"/>
                <path class="leg b" d="M37 50 V60 M34.5 60.5 H40"/>
                <path class="leg a" d="M45 49 V60 M42.5 60.5 H48"/>
            </g>
            <path d="M10 31 C4.5 34 4.5 42 8 47"/>
            <ellipse class="tuft" cx="8.2" cy="48.5" rx="2.6" ry="3.4"/>
            <rect class="fill" x="9" y="24" width="42" height="26" rx="13"/>
            <path class="spot" d="M18 29 C23 26 29 30 26 35 C23 39 16 36 18 29 Z"/>
            <path class="spot" d="M34 38 C38 35 44 38 41 43 C38 46 32 43 34 38 Z"/>
            <path class="horn" d="M55 20 C54 15 55 12 57 12 C58 15 59 17 59 19 Z"/>
            <path class="horn" d="M67 19 C67 16 68 13 70 12 C71 15 70 18 69 20 Z"/>
            <ellipse class="fill" cx="49.5" cy="28" rx="5" ry="3" transform="rotate(-25 49.5 28)"/>
            <rect class="fill" x="49" y="18" width="24" height="25" rx="11"/>
            <ellipse class="muzzle" cx="66" cy="37.5" rx="8" ry="5.4"/>
            <circle class="dot" cx="63.5" cy="37.5" r="1.1"/>
            <circle class="dot" cx="68.5" cy="37.5" r="1.1"/>
            <g class="eye"><circle cx="61.5" cy="29.5" r="2.8"/><circle class="shine" cx="62.4" cy="28.6" r="0.9"/></g>
            <circle class="cheek" cx="55.5" cy="35" r="3"/>
        </svg>
    </div>

    {{-- 豚 --}}
    <div class="walker pig" style="--walk-delay: -18s">
        <svg viewBox="0 0 80 64" focusable="false">
            <g class="legs">
                <path class="leg a" d="M22 51 V60 M19.5 60.5 H25"/>
                <path class="leg b" d="M30 53 V60 M27.5 60.5 H33"/>
                <path class="leg b" d="M40 53 V60 M37.5 60.5 H43"/>
                <path class="leg a" d="M48 51 V60 M45.5 60.5 H51"/>
            </g>
            <path d="M13 38 C8 37 7 31.5 11 30.5 C14.5 30 14 34.5 11.5 34"/>
            <ellipse class="fill" cx="33" cy="40" rx="22" ry="15"/>
            <path class="fill" d="M49 26 L48 14 L58 21"/>
            <path class="fill" d="M63 22 L71 15 L69 28"/>
            <circle class="fill" cx="58" cy="34" r="13"/>
            <ellipse class="snout" cx="68.5" cy="38" rx="6.5" ry="4.8"/>
            <circle class="dot" cx="66.5" cy="38" r="1.2"/>
            <circle class="dot" cx="70.5" cy="38" r="1.2"/>
            <g class="eye"><circle cx="60" cy="30.5" r="2.8"/><circle class="shine" cx="60.9" cy="29.6" r="0.9"/></g>
            <circle class="cheek" cx="52.5" cy="38.5" r="3.2"/>
        </svg>
    </div>

    {{-- 卵 --}}
    <div class="walker egg" style="--walk-delay: -11s">
        <svg viewBox="0 0 80 64" focusable="false">
            <g class="legs">
                <path class="leg a orange" d="M33 53 V60 M30 60.5 H36"/>
                <path class="leg b orange" d="M47 53 V60 M44 60.5 H50"/>
            </g>
            <path class="fill" d="M40 4 C54 4 63 22 63 36 C63 50 54 57 40 57 C26 57 17 50 17 36 C17 22 26 4 40 4 Z"/>
            <path class="gloss" d="M27 23 C28 18 31 14 35 11.5"/>
            <g class="eye"><circle cx="33" cy="35" r="2.9"/><circle class="shine" cx="34" cy="34" r="1"/></g>
            <g class="eye"><circle cx="47" cy="35" r="2.9"/><circle class="shine" cx="48" cy="34" r="1"/></g>
            <circle class="cheek" cx="27" cy="41.5" r="3.4"/>
            <circle class="cheek" cx="53" cy="41.5" r="3.4"/>
            <path class="smile" d="M37.5 41 Q40 44.5 42.5 41"/>
        </svg>
    </div>

    {{-- ひよこ --}}
    <div class="walker chick" style="--walk-delay: -5s">
        <svg viewBox="0 0 80 64" focusable="false">
            <g class="legs">
                <path class="leg a orange" d="M34 53 V60 M31 60.5 H37"/>
                <path class="leg b orange" d="M46 53 V60 M43 60.5 H49"/>
            </g>
            <circle class="fill" cx="40" cy="35" r="20"/>
            <path d="M37 15.5 C35 10 38 7.5 39.5 11.5 M41 15 C41.5 9 45.5 8.5 44 14.5"/>
            <path class="wing" d="M24 38 C22 30 30 27 36 32 C38 38 31 43 24 38 Z"/>
            <path class="beak" d="M58 33 L67.5 36.5 L58 40.5 Z"/>
            <g class="eye"><circle cx="50" cy="30.5" r="2.9"/><circle class="shine" cx="51" cy="29.5" r="1"/></g>
            <circle class="cheek" cx="47" cy="40" r="3.4"/>
        </svg>
    </div>
</div>
