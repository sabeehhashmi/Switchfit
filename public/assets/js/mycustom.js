/**
 * @Description jQuery and Javascript Code
 * @Author Khuram Qadeer.
 */

// on click browser back forward button ,back url would be reload
var perfEntries = performance.getEntriesByType("navigation");
if (perfEntries[0].type === "back_forward") {
    location.reload(true);
}

jQuery(document).ready(function () {
    // Default Datatable
    $('#datatable').DataTable({
        "order": [[0, "desc"]],
    });
    // users table
    $('#datatable_users').DataTable({
        "order": [[0, "desc"]],
        "bLengthChange": false,
        "pageLength": 20
    });

    // Add the following code if you want the name of the file appear on select
    $(".custom-file-input").on("change", function () {
        var fileName = $(this).val().split("\\").pop();
        $(this).siblings(".custom-file-label").addClass("selected").html(fileName);
    });
//     // setTimeout(function () {
//     $(".fileInput").change(function () {
// var el = this;
//         var imgId = $(this).attr('data-img-id')
//         document.getElementById(imgId).src = window.URL.createObjectURL(el.files[0])
//
//         // if (this.files && this.files[0]) {
//         //     var reader = new FileReader();
//         //     reader.onload = function (e) {
//         //
//         //             console.log( e.target.result);
//         //             $(imgId).attr('src', e.target.result);
//         //
//         //
//         //     }
//         //     reader.readAsDataURL(this.files[0]);
//         // }
//     });
//     // },5000)
});

/**
 * @Description Read file for image and set into image
 * @param el
 * @param imgId
 * @Author Khuram Qadeer.
 */
function readImageFile(el, imgId) {
    document.getElementById(imgId).src = window.URL.createObjectURL(el.files[0])
}

/**
 * @Description onchnage gyms files add set image into related img tag
 * @param el
 * @Author Khuram Qadeer.
 */
function readGymImageFile(el) {
    document.getElementById($(el).attr('data-img-id')).src = window.URL.createObjectURL(el.files[0]);
    if ($('.photo-list li').length < 6) {
        var counter = $('.photo-list li').length + 1;
        var img_id = 'img_file' + counter;
        var fileId = 'file' + counter;
        var img = "<a href='#'><img id='" + img_id + "' src='/assets/images/upload-img.png' alt='Placeholder'  /></a>"
        var file = " <input type='file' name='" + fileId + "' id='" + fileId + "'  data-img-id='" + img_id + "'  />"
        var li = "<li> <div class='upload-btn-wrapper'><div class='image-holder'>" + img + "</div>     " + file + "  </div> </li>"
        $('.photo-list').append(li);
        document.getElementById(fileId).setAttribute('onchange', 'readGymImageFile(this)');
    }
}


// config questionFunction
Notiflix.Confirm.Init({okButtonBackground: "#3c15a3", titleColor: '#3c15a3'});

/**
 * @Description Question Notification
 * @param title
 * @param question
 * @param redirectUrl
 * @Author Khuram Qadeer.
 */
function questionNotification(title, question, redirectUrl) {
    Notiflix.Confirm.Show(title, question, true, false,
        function () {
            // yes
            window.location.href = redirectUrl;
        }, function () {
            // No
        });
}

/**
 * @Description notification
 * @param type
 * @param message
 * @Author Khuram Qadeer.
 */
function notification(type, message) {
    if (type == 'success') {
        Notiflix.Notify.Success(message, {cssAnimationStyle: 'zoom', cssAnimationDuration: 800,});
    } else if (type == 'danger') {
        Notiflix.Notify.Failure(message, {cssAnimationStyle: 'zoom', cssAnimationDuration: 800,});
    } else if (type == 'info' || type == 'warning') {
        Notiflix.Notify.Info(message, {cssAnimationStyle: 'zoom', cssAnimationDuration: 800,});
    }
}

/**
 * @Description open google map modal for choose location
 * @Author Khuram Qadeer.
 */
function showGoogleMapModal() {
    $('#google_map').modal('show')
}

/***
 * @Description Day Select then show time picker for this dates
 * @param el
 * @Author Khuram Qadeer.
 */
function daySelect(el) {
    var time_from = '#' + $(el).attr('value') + '_start';
    var time_to = '#' + $(el).attr('value') + '_end';
    if ($(el).is(':checked')) {
        $(time_from.trim()).removeClass('div-disable');
        $(time_to.trim()).removeClass('div-disable');
    } else {
        $(time_from.trim()).addClass('div-disable');
        $(time_to.trim()).addClass('div-disable');
    }
}

/**
 * @Description Gym List Searching
 * @Author Khuram Qadeer.
 */
$("#filter_gym").keyup(function () {
    // Retrieve the input field text and reset the count to zero
    var filter = $(this).val(),
        count = 0;
    // Loop through the comment list
    $('.results_gym ').each(function () {
        // If the list item does not contain the text phrase fade it out
        if ($(this).text().search(new RegExp(filter, "i")) < 0) {
            var id = '#gym_' + $(this).attr('data-id');
            $(id).hide();
            $('.pagination').hide();
            if ($('.gym-panel:visible').length == 0) {
                $('#no_match').show()
            } else {
                $('#no_match').hide()
            }
        } else {
            var id = '#gym_' + $(this).attr('data-id');
            $(id).show();
            $('.pagination').show();
            count++;
            if ($('.gym-panel:visible').length == 0) {
                $('#no_match').show()
            } else {
                $('#no_match').hide()
            }
        }
    });
});

/**
 * @Description Get csrf token
 * @returns {*|jQuery}
 * @Author Khuram Qadeer.
 */
function getCsrfToken() {
    return $('#_token').val();
}


