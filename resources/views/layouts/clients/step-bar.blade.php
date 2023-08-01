<ol class="steps">
    @for($i = 1 ; $i <= $length; $i++)
        @php
            $classStep = '';
            if ($i < $activeStep) {
                $classStep = 'is-complete';
            } else if ($i == $activeStep) {
                $classStep = 'is-active';

                if ($activeStep == 1) {
                   $classStep .= ' left';
                } else if ($activeStep == $length) {
                    $classStep .= ' right';
                }
            }
        @endphp
        <li class="step {{ $classStep  }}" data-step="{{ $i == $activeStep ? $i : ' ' }}"></li>
    @endfor
</ol>