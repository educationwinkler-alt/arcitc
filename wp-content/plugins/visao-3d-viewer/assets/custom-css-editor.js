// JavaScript Document
jQuery(document).ready(function($) {
    // Initialize CodeMirror on the textarea
    var editor = wp.codeEditor.initialize($('#visao-css-editor'), {
        codemirror: {
            mode: 'css',
            lineNumbers: true,
            styleActiveLine: true,
            matchBrackets: true,
            indentUnit: 4
        }
    });
});