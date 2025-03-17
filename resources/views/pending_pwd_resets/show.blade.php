@component('components.modal')
    @slot('header_classes')
        bg-primary
    @endslot
    @slot('title')
        @lang('pending_pwd_resets.title')
    @endslot
    @slot('content')
        <div class="row">
            <div class="form-horizontal">
                <div class="form-group">
                    <label for="to_mails"
                           class="col-md-2 control-label">@lang('pending_pwd_resets.attributes.fullname_withmail')</label>
                    <div class="col-md-10">
                        <p class="form-control-static"> {{ $pending_pwd_reset->account->NameWithMail }}</p>
                    </div>
                </div>
                <div class="form-group">
                    <label for="to_mails"
                           class="col-md-2 control-label">@lang('pending_pwd_resets.attributes.account')</label>
                    <div class="col-md-10">
                        <p class="form-control-static"> {{ $pending_pwd_reset->user }}</p>
                    </div>
                </div>
                <div class="form-group">
                    <label for="to_mails"
                           class="col-md-2 control-label">@lang('pending_pwd_resets.attributes.ipv4')</label>
                    <div class="col-md-10">
                        <p class="form-control-static"> {{ $pending_pwd_reset->ipv4 }}</p>
                    </div>
                </div>
                <div class="form-group">
                    <label for="to_mails"
                           class="col-md-2 control-label">@lang('pending_pwd_resets.attributes.url')</label>
                    <div class="col-md-10">
                        <div class="input-group controls">
                            {!! Form::text('url', $host.'/password/reset/'.$pending_pwd_reset->token, [ 'id' => 'url','class' => 'form-control','readonly']) !!}
                            <a class="input-group-addon" id="url_copy" style="cursor:pointer;" data-clipboard-text="{{ $host.'/password/reset/'.$pending_pwd_reset->token }}"><i class="fa fa-copy"></i></a>
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <label for="to_mails"
                           class="col-md-2 control-label">@lang('pending_pwd_resets.attributes.created_at')</label>
                    <div class="col-md-10">
                        <p class="form-control-static"> {{ optional($pending_pwd_reset->created_at)->format('d/m/Y H:i') }}</p>
                    </div>
                </div>
                <div class="btn-toolbar">
                    <button type="button" class="btn btn-sm btn-secondary pull-right control-btn"
                            data-dismiss="modal"><i class="fas fa-arrow-left fa-fw"></i> @lang('common.form.back')</button>
                </div>
            </div>
        </div>

        <script src="{{ @asset('/vendor/clipboard/clipboard.min.js') }}"></script>
        <script>
            $(document).ready(function (){
                new ClipboardJS('#url_copy');
                const token_url = $('#url_copy');
                token_url.tooltip({
                    trigger: 'manual',
                    title: 'Link copiato!'
                });
                token_url.click(function () {
                    token_url.tooltip('show');
                    setTimeout(function () {
                        token_url.tooltip('hide');
                    }, 2000);
                });
            });
        </script>
    @endslot
@endcomponent
