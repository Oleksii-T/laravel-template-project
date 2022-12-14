{{--
    Title section of a page in admin panel.
    Contains breadcrumbs rendering using diglactic/laravel-breadcrumbs package.
    The breadcrumbs part may be skipped so that this file can be ommited at all.
--}}

<div class="container-fluid">
    <div class="row mb-2">
        <div class="col-sm-6">
            <div class="float-left">
                <h1 class="m-0">{{$text}}</h1>
            </div>
            @if (isset($button))
                <div class="float-left pl-3">
                    <a href="{{$button[1]}}"
                    class="btn btn-primary">{{$button[0]}}
                </a>
            </div>
            @endif
        </div>
        @if (isset($bcRoute))
            @if (is_array($bcRoute))
                {{ Breadcrumbs::render($bcRoute[0], $bcRoute[1]) }}
            @else
                {{ Breadcrumbs::render($bcRoute) }}
            @endif
        @endif
    </div>
</div>
