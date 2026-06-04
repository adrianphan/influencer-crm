@extends('layouts.app')

@section('content')
    <h1>{{ $generatedEmail->subject }}</h1>

    <style>
        .action-row {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin: 14px 0;
        }

        .btn {
            display: inline-block;
            padding: 8px 12px;
            border: 1px solid #333;
            border-radius: 6px;
            text-decoration: none;
            color: #111;
            background: #f6f6f6;
            cursor: pointer;
            font-size: 14px;
        }

        .btn:hover {
            background: #ececec;
        }

        #copy-status {
            color: #2d7d2d;
            min-height: 18px;
        }
    </style>

    <p>
        <strong>Business:</strong>
        {{ $generatedEmail->business->name }}
    </p>

    <p><strong>Subject:</strong></p>
    <p>{{ $generatedEmail->subject }}</p>

    <p><strong>Body:</strong></p>
    <textarea id="generated-body" rows="18" cols="100">{{ $generatedEmail->body }}</textarea>

    <div class="action-row">
        <button id="copy-body" class="btn" type="button">Copy Body</button>
        <button id="copy-subject" class="btn" type="button">Copy Subject</button>
        <form method="POST" action="/generated-emails/{{ $generatedEmail->id }}/create-gmail-draft" style="display:inline;">
            @csrf
            <button class="btn" type="submit">Create Gmail Draft</button>
        </form>
        <form method="POST" action="/generated-emails/{{ $generatedEmail->id }}" style="display:inline;">
            @csrf
            @method('DELETE')
            <button class="btn" type="submit" onclick="return confirm('Delete this generated message?')">Delete</button>
        </form>
        <a class="btn" href="/businesses/{{ $generatedEmail->business_id }}">Back to Business</a>
    </div>

    <p id="copy-status"></p>
    <p class="muted">Draft only: this message was generated for review and is not auto-sent.</p>
    @if($generatedEmail->draft_id)
        <p class="muted">
            Gmail Draft Created: {{ $generatedEmail->draft_id }}
            @if($generatedEmail->draft_created_at)
                on {{ $generatedEmail->draft_created_at->format('M d, Y g:i A') }}
            @endif
        </p>
    @endif

    <script>
        const copyBodyButton = document.getElementById('copy-body');
        const copySubjectButton = document.getElementById('copy-subject');
        const copyStatus = document.getElementById('copy-status');
        const messageBody = document.getElementById('generated-body');
        const messageSubject = "{{ addslashes($generatedEmail->subject) }}";

        async function copyText(textToCopy, successMessage) {
            try {
                if (navigator.clipboard && window.isSecureContext) {
                    await navigator.clipboard.writeText(textToCopy);
                } else {
                    const tempInput = document.createElement('textarea');
                    tempInput.value = textToCopy;
                    tempInput.setAttribute('readonly', '');
                    tempInput.style.position = 'absolute';
                    tempInput.style.left = '-9999px';
                    document.body.appendChild(tempInput);
                    tempInput.select();
                    document.execCommand('copy');
                    document.body.removeChild(tempInput);
                }

                copyStatus.textContent = successMessage;
            } catch (error) {
                copyStatus.textContent = 'Copy failed. Please copy manually.';
            }
        }

        copyBodyButton.addEventListener('click', async function () {
            try {
                await copyText(messageBody.value, 'Body copied.');
            } catch (error) {
                copyStatus.textContent = 'Copy failed. Please copy manually.';
            }
        });

        copySubjectButton.addEventListener('click', async function () {
            try {
                await copyText(messageSubject, 'Subject copied.');
            } catch (error) {
                copyStatus.textContent = 'Copy failed. Please copy manually.';
            }
        });
    </script>
@endsection

