
var lastClickedButtons = {};

function setRank(teamId, rank) {
    // Supprimer la classe 'selected-ranking' du dernier bouton cliqué pour cette équipe
    if (lastClickedButtons[teamId]) {
        lastClickedButtons[teamId].classList.remove('selected-ranking');
    }

    // Ajouter la classe 'selected-ranking' au bouton cliqué
    var clickedButton = document.getElementById('ranking-button-' + teamId + '-' + rank);
    clickedButton.classList.add('selected-ranking');

    // Mettre à jour le dernier bouton cliqué pour cette équipe
    lastClickedButtons[teamId] = clickedButton;

    // Envoyer une requête AJAX pour enregistrer le classement
    var xhr = new XMLHttpRequest();
    xhr.open("POST", "{{ path('app_meeting_update_ranking', {'teamId': teamId, 'rank': rank}) }}", true);
    xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
    xhr.setRequestHeader('Content-Type', 'application/json');
    xhr.onload = function () {
        if (xhr.status === 200) {
            console.log('Classement enregistré avec succès');
        } else {
            console.error('Erreur lors de l\'enregistrement du classement');
        }
    };
    xhr.send();
}

document.addEventListener('DOMContentLoaded', function () {
    const rankingContainer = document.getElementById('ranking-container');
    const postUrl = rankingContainer.getAttribute('data-post-url');
    const meetingId = rankingContainer.getAttribute('data-meeting-id');
    const buttons = document.querySelectorAll('.btn-ranking');

    buttons.forEach(button => {
        button.addEventListener('click', function () {
            const teamId = button.dataset.teamId;
            const rank = button.dataset.rank;

            document.querySelectorAll(`button[data-rank="${rank}"]`).forEach(btn => {
                btn.classList.remove('selected-ranking');
            });
            button.classList.add('selected-ranking');

            fetch(postUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify({ teamId: teamId, rank: rank, meetingId: meetingId })
            })
                .then(response => response.json())
                .then(data => {
                    console.log('Classement enregistré avec succès', data);
                })
                .catch(error => {
                    console.error('Erreur lors de l\'enregistrement du classement', error);
                });
        });
    });
});

