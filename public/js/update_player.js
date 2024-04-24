
function removePlayer(username, teamIndex) {
    fetch(`/tournament/${tournamentId}/remove_player/${username}`, {
        method: 'POST'
    })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                document.location.reload(); // Rafraîchir la page pour voir les modifications
            }
        });
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
