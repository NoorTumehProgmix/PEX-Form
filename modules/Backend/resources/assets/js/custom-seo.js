$(document).ready(function () {

    $("body").on('click', '.custom-seo', function () {

        let item = $(this);
        let title = $('input[name=title]').val();
        let description = tinyMCE.get('content-editor').getContent();

        // let description = tinyMCE.activeEditor.getContent();
        if ($("#meta_title").val() && $("#meta_description").val()) {
            item.hide('slow');
            $(".box-custom-seo").show('slow');
            return false;
        }
    });

    $(document).on('change',
        'input[name="meta_og_title"], textarea[name="meta_og_description"]',
        function () {
            var newValue = $(this).val();
            var targetClass = $(this).attr('name');
            if ($('.' + targetClass).length > 0) {
                $('.' + targetClass).text(newValue);
            }
        });

    //On change main page title
    $("input[name=title]").on('change', function () {
        if ($("#model_exists").val() == "") {
            let title = $('input[name=title]').val();
            $("#meta_title").val(title);
            $("#meta_heading").val(title);
            $(".review-title").text(title);

            $("#meta_og_title").val(title);
            $(".meta_og_title").text(title);

            $("#meta_twitter_title").val(title);
        }
    });

    $("#meta_title, #meta_description").on('change', function () {
        let title = $('#meta_title').val();
        let description = $('#meta_description').val();
        $(".review-title").text(title);
        $(".review-description").text(description);
    });

    $(document).on('change', '#meta_description', function () {
        if ($('#meta_og_description').val() == "") {
            $('#meta_og_description').val($(this).val());
        }
        if ($('#meta_twitter_description').val() == "") {
            $('#meta_twitter_description').val($(this).val());
        }
    });

    var showRobotsRadio = document.querySelectorAll('input[name="meta_showRobots"]');
    var robotsOptionsDiv = document.getElementById('robotsOptions');

    showRobotsRadio.forEach(function (radio) {
        radio.addEventListener('change', function () {
            if (this.value === 'yes') {
                robotsOptionsDiv.style.display = 'block';
            } else {
                robotsOptionsDiv.style.display = 'none';
            }
        });
    });

    $('input[name="thumbnail"]').on('change', function () {
        var newValue = $(this).val(); // Get the new value
        $('input[name="meta_og_image"]').val(newValue);
    });
});
