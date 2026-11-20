import $ from 'jquery';

$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
        Accept: 'application/json',
    },
});

const loginOnUnauthorized = (xhr) => {
    if (xhr.status === 401) {
        window.location.href = '/login';
    }
};

$(document).on('submit', 'form.js-vote', function (event) {
    event.preventDefault();
    const $form = $(this);
    const key = $form.data('votable');

    $.post($form.attr('action'), $form.serialize())
        .done(({ points, voted }) => {
            $(`[data-points="${key}"]`).text(
                `${points} point${points === 1 ? '' : 's'}`,
            );
            $(`form.js-vote[data-votable="${key}"][data-how="up"]`).toggleClass(
                'nosee',
                voted,
            );
            $(`form.js-vote[data-votable="${key}"][data-how="un"]`).prop(
                'hidden',
                !voted,
            );
        })
        .fail(loginOnUnauthorized);
});

$(document).on('click', '.togg', function (event) {
    event.preventDefault();
    const $comment = $(this).closest('.comtr').toggleClass('coll');
    const count = $comment.find('.comtr').length + 1;
    $(this).text($comment.hasClass('coll') ? `[+${count}]` : '[-]');
});

$(document).on('click', '.js-reply', function (event) {
    event.preventDefault();
    $(this)
        .siblings('.replyform')
        .toggleClass('open')
        .find('textarea')
        .trigger('focus');
});

$(document).on('submit', 'form.js-comment', function (event) {
    event.preventDefault();
    const $form = $(this);
    const $errors = $form.find('.formerrors').empty();

    $.post($form.attr('action'), $form.serialize())
        .done((html) => {
            $($form.data('target')).prepend(html);
            $form.find('textarea').val('');
            $form.removeClass('open');
        })
        .fail((xhr) => {
            loginOnUnauthorized(xhr);
            const errors = xhr.responseJSON?.errors ?? {
                body: ['Something went wrong. Please try again.'],
            };
            Object.values(errors)
                .flat()
                .forEach((message) => $('<p>').text(message).appendTo($errors));
        });
});

$('#show-politics').on('change', function () {
    document.cookie = `show_politics=${this.checked ? 1 : 0}; path=/; max-age=31536000; SameSite=Lax`;
    window.location.reload();
});
