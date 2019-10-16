$(document).ready(function() {

    $(document).on("click", "span.navigation-toggle-outer", function() {

        openNav();

        return false;
    });

    $(document).on("click", "button.close-message-button", function() {

        $("div.header-message").addClass("gone");

        return false;
    });

    $(document).on("click", "button.close-privacy-message", function() {

        $("div.header-message").addClass("gone");

        $.cookie("privacy", "close", { expires : 7, path: '/' });

        return false;
    });
});