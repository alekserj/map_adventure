function addReviewToList(review) {
    const reviewsList = document.getElementById('reviews-list');
    const li = document.createElement('li');
    li.className = 'view__reviews-menu-item';

    const date = new Date(review.created_at);
    const formattedDate = `${date.getDate()}.${date.getMonth() + 1}.${date.getFullYear()} ${date.getHours()}:${date.getMinutes().toString().padStart(2, '0')}`;
    
    li.innerHTML = `
        <div class="view__reviews-menu-user">
            <p class="view__reviews-menu-name">@${review.nickname}</p>
            <p class="view__reviews-menu-date">${formattedDate}</p>
        </div>
        <p class="view__reviews-menu-text">${review.review}</p>
    `;

    reviewsList.insertBefore(li, reviewsList.firstChild);
}

function loadReviews(objectId) {
    if (!objectId) {
        console.error('Object ID is missing');
        return;
    }

    fetch(`/include/get_reviews.php?object_id=${objectId}`)
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.json();
        })
        .then(reviews => {
            const reviewsList = document.getElementById('reviews-list');
            if (!reviewsList) {
                console.error('Reviews list element not found');
                return;
            }

            reviewsList.innerHTML = '';

            if (reviews.length === 0) {
                const emptyMessage = document.createElement('li');
                emptyMessage.className = 'view__reviews-menu-empty';
                emptyMessage.textContent = 'Пока нет отзывов. Будьте первым!';
                reviewsList.appendChild(emptyMessage);
                return;
            }

            reviews.forEach(review => {
                addReviewToList(review);
            });
        })
        .catch(error => {
            console.error('Error loading reviews:', error);
            const reviewsList = document.getElementById('reviews-list');
            if (reviewsList) {
                reviewsList.innerHTML = '<li class="view__reviews-menu-error">Не удалось загрузить отзывы</li>';
            }
        });
}

document.getElementById('send_review').addEventListener('click', function(e) {
    e.preventDefault();
    
    const reviewText = document.getElementById('review').value.trim();
    const objectId = document.getElementById('review-object-id').value;

    fetch('/include/check-auth.php')
        .then(response => response.json())
        .then(authData => {
            if (!authData.isAuth) {
                alert('Для отправки отзыва необходимо авторизоваться');
                document.querySelector("#account-menu").classList.add("menu-is-active");
                document.querySelector("#reviews-menu").classList.toggle("menu-is-active");
                return;
            }

            if (!reviewText) {
                alert('Текст отзыва не может быть пустым');
                return;
            }

            const formData = new FormData();
            formData.append('review', reviewText);
            formData.append('object_id', objectId);
            
            fetch('/include/send_review.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    addReviewToList(data.review);
                    document.getElementById('review').value = '';
                } else {
                    alert(data.message);
                }
            })
            .catch(error => {
                console.error('Ошибка:', error);
                alert('Произошла ошибка при отправке отзыва');
            });
        });
});

document.querySelector('#addReview')?.addEventListener("click", function() {
    const objectId = document.getElementById("informationId").value;
    document.querySelector("#reviews-menu").classList.toggle("menu-is-active");
    document.querySelector("#review-object-id").value = objectId;
    loadReviews(objectId);
});