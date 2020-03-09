$(document).ready(function() {
  $('#filer_input').filer({
    showThumbs: true,
    addMore: true,
    captions: {
      button: 'เลือกไฟล์',
      feedback: 'Choose files To Upload',
      feedback2: 'files were chosen',
      drop: 'Drop file here to Upload',
      removeConfirmation: 'Are you sure you want to remove this file?',
      errors: {
        filesLimit: 'Only {{fi-limit}} files are allowed to be uploaded.',
        filesType: 'Only Images are allowed to be uploaded.',
        filesSize:
          '{{fi-name}} is too large! Please upload file up to {{fi-fileMaxSize}} MB.',
        filesSizeAll:
          "Files you've choosed are too large! Please upload files up to {{fi-maxSize}} MB.",
        folderUpload: 'You are not allowed to upload folders.'
      }
    }
  })
})
