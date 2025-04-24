document.addEventListener('DOMContentLoaded', function() {
  const addImageBtn = document.getElementById('addObjectInformationImg');
  const fileInput = document.getElementById('fileInput');
  const pictureList = document.getElementById('pictureList');
  const form = document.getElementById('add-information-menu-form');
  
  const selectedImages = [];
  
  addImageBtn.addEventListener('click', function() {
      fileInput.click();
  });
  
  fileInput.addEventListener('change', function(e) {
      const files = e.target.files;
      
      for (let i = 0; i < files.length; i++) {
          const file = files[i];
          
          if (file.type.match('image.*')) {
              const reader = new FileReader();
              
              reader.onload = function(e) {
                  const imageData = {
                      id: Date.now() + i,
                      file: file,
                      preview: e.target.result,
                      name: file.name
                  };

                  selectedImages.push(imageData);

                  const listItem = document.createElement('li');
                  listItem.className = 'view__picture-item';
                  listItem.dataset.id = imageData.id;
                  
                  const div = document.createElement('div');
                  div.className = "view__picture-group";

                  const img = document.createElement('img');
                  img.src = imageData.preview;
                  img.className = 'view__picture-preview';
                  
                  const removeBtn = document.createElement('button');
                  removeBtn.textContent = '×';
                  removeBtn.className = 'view__picture-remove';
                  removeBtn.addEventListener('click', function() {

                      const index = selectedImages.findIndex(img => img.id == listItem.dataset.id);
                      if (index !== -1) {
                          selectedImages.splice(index, 1);
                      }
                      listItem.remove();
                  });
                  
                  div.appendChild(img);
                  div.appendChild(removeBtn);
                  listItem.appendChild(div);
                  pictureList.appendChild(listItem);
              };
              
              reader.readAsDataURL(file);
          }
          console.log(selectedImages)
      }
      fileInput.value = '';
  });

  form.addEventListener('submit', function(e) {
      e.preventDefault();

      const formData = new FormData(form);
      
      selectedImages.forEach((image, index) => {
          formData.append(`images[${index}]`, image.file);
      });
      
      fetch('../include/save_object.php', {
          method: 'POST',
          body: formData
      })
      .then(response => {
          if (!response.ok) {
              return response.text().then(text => {
                  throw new Error(text || 'Ошибка сервера');
              });
          }
          return response.json();
      })
      .then(data => {
          if (data.success) {
              const msg = [
                  data.description_updated && 'Описание обновлено',
                  data.images_uploaded > 0 && `Добавлено ${data.images_uploaded} изображений`
              ].filter(Boolean).join('\n') || 'Данные сохранены';
              
              alert(msg);
              form.reset();
              pictureList.innerHTML = '';
              selectedImages.length = 0;
              window.location.reload();
          } else {
              throw new Error(data.message || 'Ошибка сохранения');
          }
      })
      .catch(error => {
          console.error('Ошибка:', error);
          const errorMsg = error.message.split('\n')[0];
          alert('Произошла ошибка: ' + errorMsg);
      });
  });
});