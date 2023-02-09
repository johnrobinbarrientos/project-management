<template>

  <div :id="id" class="modal" :ref="id">
  <!-- Modal content -->
  <div class="modal-content">
    <div class="modal-header">{{ title }}</div>
    <div class="modal-body"><slot name="body"></slot></div>
    <div class="modal-footer">
      <button @click="close()" class="btn-bp btn-bp-gray">Cancel</button>
      <slot name="footer"></slot>
    </div>
  </div>

</div>
</template>

<!-- BlogPost.vue -->
<script>
export default {
  props: ['id','title'],
  methods: {    
      close: function() {
        var scope = this
       scope.$refs[scope.id].classList.remove('open')
      }
  },
  mounted() {
    var scope = this
    /*
    console.log('1 ss=>',scope.$refs[scope.id].classList)
    scope.$refs[scope.id].classList.add('my-class')
    console.log('2 ss=>',scope.$refs[scope.id].classList)  
    scope.$refs[scope.id].classList.remove('my-class')
    console.log('2 ss=>',scope.$refs[scope.id].classList)  
    */


    var modal = document.getElementById(scope.id);
    // When the user clicks anywhere outside of the modal, close it
    window.onclick = function(event) {
      if (event.target == modal) {
        scope.$refs[scope.id].classList.remove('open')
      }
    }
  }
}
</script>

<style scoped>
/* The Modal (background) */
.modal {
  display: none; /* Hidden by default */
  position: fixed; /* Stay in place */
  z-index: 1; /* Sit on top */
  left: 0;
  top: 0;
  width: 100%; /* Full width */
  height: 100%; /* Full height */
  overflow: auto; /* Enable scroll if needed */
  background-color: rgb(0,0,0); /* Fallback color */
  background-color: rgba(0,0,0,0.6); /* Black w/ opacity */
}

.modal.open { display:block; }

/* Modal Content/Box */
.modal-content {
  background-color: #fafbfc;
  margin: 30px auto; /* 15% from the top and centered */
  padding: 0px;
  border: 1px solid #888;
  width: calc(100% - 40px); /* Could be more or less, depending on screen size */
  max-width:700px;
  border-radius: 10px;
}

.modal-header { 
  border-top-right-radius: 10px;
  border-top-left-radius: 10px;
  padding:10px 20px;
  border-bottom:1px solid #e9ebf0;
  font-size:20px; 
  font-weight:600;
  background:#fff;
}
.modal-body { 
  padding: 20px 20px 20px;
  max-height: calc(100vh - 300px);
  overflow-y: auto;
}
.modal-footer { 
  border-bottom-right-radius: 10px;
  border-bottom-left-radius: 10px;
  padding:10px 36px;
  padding-bottom:30px;
  font-size:20px; 
  font-weight:600;
  text-align:right;
}

/* The Close Button */
.close {
  color: #aaa;
  float: right;
  font-size: 28px;
  font-weight: bold;
}

.close:hover,
.close:focus {
  color: black;
  text-decoration: none;
  cursor: pointer;
}

</style>