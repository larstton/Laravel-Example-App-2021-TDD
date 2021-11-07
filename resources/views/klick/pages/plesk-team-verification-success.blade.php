@extends('klick.layouts.app')

@section('body')
    <x-centered-card>
        <p>Your account has been verified and you can start monitoring your Plesk managed server now.</p>

        <p>To complete the setup do the following</p>

        <ol class="wiki-list0">
            <li>
                Copy the following token to the clipboard: <span class="mdl-chip mdl-chip--contact token-value" id="token-wrapper" onclick="copyTextToClipboard('token-value', 'token-wrapper')">
                    <span class="mdl-chip__contact success mdl-color-text--white">
                        <i aria-hidden="true" class="v-icon material-icons theme--light white--text success" style="font-size: 16px;">file_copy</i>
                    </span>
                    <span class="mdl-chip__text" id="token-value">{{ $apiToken->token }}</span>
                    <label for="token-wrapper" class="mdl-tooltip">Copy to clipboard</label>
                </span>
            </li>
            <li>Enter it on the CloudRadar Plesk extension under the section "I have an API token"</li>
            <li>Click on "Register host and start monitoring" on the Plesk extension</li>
            <li>Goto <a href="{{ $url = route('web.login', ['email' => $user->email]) }}" target="_blank" rel="noopener noreferrer">{{ $url }}</a> to review your data and fine-tune alerting.</li>
        </ol>


        <script type="text/javascript">
            /**
             * @see https://stackoverflow.com/questions/400212/how-do-i-copy-to-the-clipboard-in-javascript
             * @param copy_id element is which content to copy
             * @param wrapper_id element id which class to set in case of success
             *
             */
            function copyTextToClipboard(copy_id,wrapper_id) {
                var el  = document.getElementById(copy_id);
                var text = el.innerText;
                var textArea = document.createElement("textarea");
                textArea.style.position = 'fixed';
                textArea.style.top = 0;
                textArea.style.left = 0;
                textArea.style.width = '2em';
                textArea.style.height = '2em';
                textArea.style.padding = 0;
                textArea.style.border = 'none';
                textArea.style.outline = 'none';
                textArea.style.boxShadow = 'none';
                textArea.style.background = 'transparent';
                textArea.value = text;

                document.body.appendChild(textArea);
                textArea.focus();
                textArea.select();

                try {
                    var successful = document.execCommand('copy');
                    var msg = successful ? 'successful' : 'unsuccessful';
                    document.getElementById(wrapper_id).classList.add('success');
                    console.log('Copying text command was ' + msg);
                } catch (err) {
                    console.log('Oops, unable to copy');
                }

                document.body.removeChild(textArea);
            }
        </script>
    </x-centered-card>
@endsection
