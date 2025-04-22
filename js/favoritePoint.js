function attachFavoriteButtonHandler(pointId) {
    setTimeout(() => {
      const button = document.querySelector('#addFavoritePoint');
      if (button) {
        button.onclick = () => {
          addToFavorites(pointId);
        };
      }
    }, 100);
  }
  
  function addToFavorites(pointId) {
    fetch('../include/auth.php')
      .then(response => response.json())
      .then(data => {
        if (!data.isAuth) {
          alert('Для добавления в избранное необходимо авторизоваться');
          return;
        }
        
        fetch('../include/save_point.php', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
          },
          body: `point_id=${pointId}`
        })
        .then(response => response.json())
        .then(data => {
          if (data.success) {
            const button = document.querySelector('#addFavoritePoint');
            if (button) {
              if (data.action === 'added') {
                button.classList.remove('baloon__favorite-grey');
                button.classList.add('baloon__favorite-gold');
              } else {
                button.classList.remove('baloon__favorite-gold');
                button.classList.add('baloon__favorite-grey');
              }
            }
            alert(data.message);
          } else {
            alert(data.message || 'Произошла ошибка');
          }
        })
        .catch(error => {
          console.error('Error:', error);
          alert('Произошла ошибка при работе с избранным');
        });
      })
      .catch(error => {
        console.error('Error:', error);
      });
  }