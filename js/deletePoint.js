document.addEventListener('DOMContentLoaded', function() {
    document.getElementById('objects-admin-list').addEventListener('click', async function(e) {
        if (e.target.classList.contains('delete-btn')) {
            const pointId = e.target.getAttribute('data-id');
            if (confirm('Вы уверены, что хотите удалить этот объект?')) {
                try {
                    const response = await fetch('/include/delete_point.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded',
                        },
                        body: 'point_id=' + pointId
                    });
                    
                    const text = await response.text();
                    
                    try {
                        const data = JSON.parse(text);
                        
                        if (data.success) {
                            e.target.closest('.view__reviews-menu-item').remove();
                            const placemarkIndex = window.placemarks.findIndex(p => p.pointData.id == pointId);
                            if (placemarkIndex !== -1) {
                                window.myMap.geoObjects.remove(window.placemarks[placemarkIndex].placemark);
                                window.placemarks.splice(placemarkIndex, 1);
                            }
                            alert('Объект успешно удален');
                        } else {
                            alert('Ошибка: ' + (data.message || 'Неизвестная ошибка'));
                        }
                    } catch (e) {
                        console.error('Ошибка парсинга JSON:', text);
                        alert('Сервер вернул невалидный JSON: ' + text);
                    }
                } catch (error) {
                    console.error('Ошибка:', error);
                    alert('Ошибка при удалении: ' + error.message);
                }
            }
        }
    });
});