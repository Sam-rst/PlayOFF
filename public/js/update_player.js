
document.addEventListener('DOMContentLoaded', function () {
    const tournamentInfo = document.getElementById('tournament-info');
    const tournamentId = tournamentInfo.getAttribute('data-tournament-id');

    const removeButtons = document.querySelectorAll('.remove-player');
    removeButtons.forEach(button => {
        button.addEventListener('click', function () {
            const username = this.getAttribute('data-username');
            const teamIndex = this.getAttribute('data-index'); // S'il est utilisé
            removePlayer(username, teamIndex, tournamentId);
        });
    });
});

function removePlayer(username, teamIndex, tournamentId) {
    fetch(`/tournament/${tournamentId}/remove_player/${username}`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Suppression visuelle de l'élément de la liste
            document.querySelector(`li[data-username="${username}"]`).remove();
        } else {
            console.error('Failed to delete the player');
        }
    })
    .catch(error => console.error('Error:', error));
}


function initSwap(username, teamIndex) {
    // Supposons que nous utilisons une boîte de dialogue simple pour choisir avec qui échanger
    const otherUsername = prompt("Entrez le nom d'utilisateur avec qui échanger :");
    if (otherUsername) {
        swapPlayers(username, otherUsername);
    }
}

function swapPlayers(username1, username2) {
    fetch(`/tournament/${tournamentId}/swap_players`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-Token': 'TOKEN_HERE' // Assurez-vous d'inclure le token CSRF si nécessaire
        },
        body: JSON.stringify({ username1, username2 })
    })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                console.log('Échange réussi');
                document.location.reload(); // Rafraîchir la page pour voir les modifications
            } else {
                console.error('Échec de l’échange');
            }
        });
}
