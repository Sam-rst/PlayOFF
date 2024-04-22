document.addEventListener('DOMContentLoaded', function() {
    var toastLiveExample = document.getElementById('liveToast');

    if (toastLiveExample) {
        var toast = new bootstrap.Toast(toastLiveExample);
        toast.show();

        // Fermer le toast lorsqu'on clique sur le bouton de fermeture
        var toastCloseButton = toastLiveExample.querySelector('.btn-close');
        if (toastCloseButton) {
            toastCloseButton.addEventListener('click', function() {
                toast.hide();
            });
        }

        // Disparaître le toast après un certain temps
        var timeout = 5000; // Durée en millisecondes (5 secondes)
        setTimeout(function() {
            toast.hide();
        }, timeout);
    }
});
