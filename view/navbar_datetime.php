   <?php require_once "utils/AppTime.php" ?>
   <nav class="navbar bg-transparent" style="backdrop-filter: blur(20px);">
       <div class="container-fluid">
           <div class="navbar-brand text-white">
               <span><i class="bi bi-clock"></i> <?= AppTime::get_current_datetime() ?></span>
           </div>
           <ul class="list-group list-group-horizontal">
               <li class="list-group-item border-0 bg-transparent py-0 px-1">
                   <form method="post" action="time/advance" style="display: inline;">
                       <input type="hidden" name="amount" value="1">
                       <input type="hidden" name="unit" value="hour">
                       <button type="submit" class="btn btn-warning opacity-75"><i class="bi bi-clock"></i> +1h</button>
                   </form>
               </li>
               <li class="list-group-item border-0 bg-transparent py-0 px-1">
                   <form method="post" action="time/advance" style="display: inline;">
                       <input type="hidden" name="amount" value="1">
                       <input type="hidden" name="unit" value="day">
                       <button type="submit" class="btn btn-warning opacity-75"><i class="bi bi-calendar4-event"></i> +1d</button>
                   </form>
               </li>
               <li class="list-group-item border-0 bg-transparent py-0 px-1">
                   <form method="post" action="time/advance" style="display: inline;">
                       <input type="hidden" name="amount" value="7">
                       <input type="hidden" name="unit" value="day">
                       <button type="submit" class="btn btn-warning opacity-75"><i class="bi bi-calendar2-plus"></i> +1w</button>
                   </form>
               </li>
               <li class="list-group-item border-0 bg-transparent py-0 px-1">
                   <form method="post" action="time/reset" style="display: inline;">
                       <button type="submit" class="btn btn-danger opacity-75"><i class="bi bi-arrow-clockwise"></i> Reset</button>
                   </form>
               </li>
           </ul>
       </div>
   </nav>