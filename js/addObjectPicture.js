document.addEventListener('DOMContentLoaded', function() {
    const addImageBtn = document.getElementById('addObjectInformationImg')
    const fileInput = document.getElementById('fileInput')
    const pictureList = document.getElementById('pictureList')
    const form = document.getElementById('add-information-menu-form')
    
    addImageBtn.addEventListener('click', function() {
      fileInput.click()
    })
    
    fileInput.addEventListener('change', function(e) {
      const files = e.target.files
      
      for (let i = 0; i < files.length; i++) {
        const file = files[i]
        
        if (file.type.match('image.*')) {
          const reader = new FileReader()
          
          reader.onload = function(e) {
            const listItem = document.createElement('li')
            listItem.className = 'view__picture-item'
            listItem.id = 'picture-item'
            
            const div = document.createElement('div')
            div.className ="view__picture-group"

            const img = document.createElement('img')
            img.src = e.target.result
            img.className = 'view__picture-preview'
            
            const removeBtn = document.createElement('button')
            removeBtn.textContent = '×'
            removeBtn.className = 'view__picture-remove'
            removeBtn.addEventListener('click', function() {
              listItem.remove()
            });
            
            div.appendChild(img)
            div.appendChild(removeBtn)
            listItem.appendChild(div)
            pictureList.appendChild(listItem)
          }
          
          reader.readAsDataURL(file)
        }
      }
    })
  })