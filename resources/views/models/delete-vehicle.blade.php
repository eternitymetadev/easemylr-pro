<div class="modal fade" id="deletevehicle" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
     <div class="modal-content p-3">
       <!-- Modal body -->
           <div class="modal-body">
               <div class="Delt-content text-center">
                   <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                        stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                        class="feather feather-alert-circle deleteAlertIcon">
                       <circle cx="12" cy="12" r="10"></circle>
                       <line x1="12" y1="8" x2="12" y2="12"></line>
                       <line x1="12" y1="16" x2="12.01" y2="16"></line>
                   </svg>
                   <h5 class="my-2">Delete Vehicle</h5>
                   <span>Are you sure you want to delete this vehicle?</span>
               </div>
           </div>
       <!-- Modal footer -->
         <div class="d-flex justify-content-end align-content-center mt-4" style="gap: 1rem;">
             <button type="button" class="btn btn-outline-danger" data-dismiss="modal">Cancel</button>
             <button type="submit" id="location_savebtn"
                     class="btn btn-cstm btn-danger btn-modal delete-btn-modal deletevehicleconfirm">Yeah! Sure
             </button>
         </div>
     </div>
   </div>
</div>
