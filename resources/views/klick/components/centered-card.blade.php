<!-- Wide card with share menu button -->
<style>
    .demo-card-wide.mdl-card {
        width: 512px;
    }

    .demo-card-wide > .mdl-card__title {
        color: #fff;
        height: 176px;
        background: url({{ asset('/assets/images/welcome_card.jpg') }}) center / cover;
    }

    .demo-card-wide > .mdl-card__menu {
        color: #fff;
    }

    .mdl-grid.center-items {
        justify-content: center;
    }

    .token-value {
        cursor: pointer;
    }

    .success {
        background-color: #2d9437;
        border-color: #2d9437;
    }
</style>

<div class="mdl-grid center-items">
    <div class="demo-card-wide mdl-card mdl-shadow--2dp mdl-cell mdl-cell--4-col">
        <div class="mdl-card__title">
            <h2 class="mdl-card__title-text">{{ $title }}</h2>
        </div>
        <div class="mdl-card__supporting-text">
            {!! $slot !!}
        </div>

        @if($hasLink())

            <div class="mdl-card__actions mdl-card--border">
                <a href="{{ $getLinkTarget() }}"
                   class="mdl-button mdl-button--colored mdl-js-button mdl-js-ripple-effect">
                    {{ $getLinkText() }}
                </a>
            </div>

        @elseif($hasForm())

            <div class="mdl-card__actions mdl-card--border mdl-grid">
                <form
                    action="{{ $getFormAction() }}"
                    method="{{ $getFormMethod() }}"
                    class="mdl-cell mdl-cell--8-col mdl-cell--4-offset mdl-typography--text-right"
                >
                    <button
                        class="mdl-button mdl-js-button mdl-button--raised mdl-js-ripple-effect mdl-button--primary"
                    >
                        {{ $getFormButtonText() }}
                    </button>
                </form>
            </div>
            
        @endif
    </div>
</div>
