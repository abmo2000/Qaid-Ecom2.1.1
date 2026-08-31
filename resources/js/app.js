
import Alpine from 'alpinejs'

// Check if Alpine is already initialized
document.addEventListener('DOMContentLoaded' , function(){
  if (!window.Alpine) {
      window.Alpine = Alpine
      Alpine.start()
  }
});

import '@web/slider'
import '@web/cart'
 import '@web/order'