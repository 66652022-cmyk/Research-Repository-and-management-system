<textarea id="manuscript-editor"></textarea>
<script src="https://cdn.tiny.cloud/1/no-api-key/tinymce/6/tinymce.min.js"></script>
<script>
tinymce.init({
    selector: '#manuscript-editor',
    height: 600,
    plugins: 'lists table link codesample',
    toolbar: 'undo redo | bold italic underline | saveBtn',
    setup: function(editor) {
        editor.ui.registry.addButton('saveBtn', {
            text: 'Save',
            onAction: function() {
                const content = editor.getContent();
                fetch('save_version.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({
                        document_id: window.currentDocId,
                        content: content
                    })
                }).then(res => res.json()).then(d => alert(d.message));
            }
        });
    }
});

// Tatanggapin yung doc galing sa parent
window.addEventListener('message', (event) => {
    if (event.data.type === 'loadDocument') {
        window.currentDocId = event.data.document.document_id;
        tinymce.get('manuscript-editor').setContent(event.data.document.content || '');
    }
});
</script>
