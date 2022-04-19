<?php
if (!defined('ABSPATH')) {
    die();
}

global $FileManager;

$admin_page_url = admin_url() . "admin.php?page={$FileManager->prefix}";

// Enqueing admin assets
$FileManager->admin_assets();
// delete_option('fm_log');delete_option('fm_log2');
$logs = get_option('fm_log', array());
// Language
include 'language-code.php';
global $fm_languages;
?>
<?php require_once 'header.php';?>
<div class='fm-container'>
	<div class='col-main' >
		<div class='gb-fm-row fmp-settings'>
        <h2><?php _e("File Manger Log", 'file-manager');?></h2>
        <!-- <table id="customers">
            <tr>
                <th>Sr</th>
                <th>Date</th>
                <th>Command</th>
                <th>Key</th>
                <th>Error</th>
            </tr>
            <?php
//foreach($logs as $key => $log){?>
                <tr>
                    <td><?php echo $key + 1; ?></td>
                    <td><?php echo $log['date']; ?></td>
                    <td><?php echo $log['cmd']; ?></td>
                    <td><?php echo (isset($log['key']) && !empty($log['key'])) ? $log['key'] : ""; ?></td>
                    <td><?php print_r((isset($log['err']) && !empty($log['err'])) ? $log['err'] : "");?></td>
                </tr>
            <?php// } ?>

            </table> -->

            <style>
                table th , table td{
                text-align: center;
                }

                table tr:nth-child(even){
                background-color: #BEF2F5
                }

                /* .pagination li:hover{
                cursor: pointer;
                } */
                    table tbody tr {
                        display: none;
                }
                .center {
  text-align: center;
}

.pagination {
  display: inline-block;
}

.pagination li {
  color: black;
  float: left;
  padding: 8px 16px;
  text-decoration: none;
  transition: background-color .3s;
  border: 1px solid #ddd;
  margin: 0 4px;
}

.pagination li.active {
  background-color: #4CAF50;
  color: white;
  border: 1px solid #4CAF50;
  cursor : pointer;
}

.pagination li:hover:not(.active) 
{
    background-color: #ddd;
    cursor : pointer;
}
                </style>


            <div class="form-group"> 	<!--		Show Numbers Of Rows 		-->
            <select class="form-control" name="state" id="maxRows">
                        <option value="5000">Show ALL Rows</option>
						 <option value="5">5</option>
						 <option value="10">10</option>
						 <option value="15">15</option>
						 <option value="20">20</option>
						 <option value="50">50</option>
						 <option value="70">70</option>
						 <option value="100">100</option>
                </select>
            </div>

                <table class="table table-striped table-class customers" id= "table-id">

                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Command</th>
                        <th>Key</th>
                        <th>Error</th>
                    </tr>

                </thead>

                <tbody>
                <?php foreach($logs as $key => $log){?>
                    <tr>
                        <td><?php echo $log['date']; ?></td>
                        <td><?php echo $log['cmd']; ?></td>
                        <td><?php echo (isset($log['key']) && !empty($log['key'])) ? $log['key'] : ""; ?></td>
                        <td><?php print_r((isset($log['err']) && !empty($log['err'])) ? $log['err'] : "");?></td>
                    </tr>
                <?php } ?>
                </tbody>
            </table>
            <div class="center pagination-container">
            <ul class="pagination">
            <li data-page="prev">
                    <span> &lt; <span class="sr-only"></span></span>
                </li>
            <li  class="active"><span><span class="sr-only"></span></span>								</li><li data-page="3">								  <span>3<span class="sr-only">(current)</span></span>								</li><li data-page="next" id="prev" style="display: inline;">
                    <span> &gt; <span class="sr-only"></span></span>
</li>
</ul>
</div>
            <script>
                    getPagination('#table-id');
					//getPagination('.table-class');
					//getPagination('table');

                            /*					PAGINATION 
                            - on change max rows select options fade out all rows gt option value mx = 5
                            - append pagination list as per numbers of rows / max rows option (20row/5= 4pages )
                            - each pagination li on click -> fade out all tr gt max rows * li num and (5*pagenum 2 = 10 rows)
                            - fade out all tr lt max rows * li num - max rows ((5*pagenum 2 = 10) - 5)
                            - fade in all tr between (maxRows*PageNum) and (maxRows*pageNum)- MaxRows 
                            */
                            

                    function getPagination(table) {
                    var lastPage = 1;

                    jQuery('#maxRows')
                        .on('change', function(evt) {
                        //jQuery('.paginationprev').html('');						// reset pagination

                        lastPage = 1;
                        jQuery('.pagination')
                            .find('li')
                            .slice(1, -1)
                            .remove();
                        var trnum = 0; // reset tr counter
                        var maxRows = parseInt(jQuery(this).val()); // get Max Rows from select option

                        if (maxRows == 5000) {
                            jQuery('.pagination').hide();
                        } else {
                            jQuery('.pagination').show();
                        }

                        var totalRows = jQuery(table + ' tbody tr').length; // numbers of rows
                        jQuery(table + ' tr:gt(0)').each(function() {
                            // each TR in  table and not the header
                            trnum++; // Start Counter
                            if (trnum > maxRows) {
                            // if tr number gt maxRows

                            jQuery(this).hide(); // fade it out
                            }
                            if (trnum <= maxRows) {
                            jQuery(this).show();
                            } // else fade in Important in case if it ..
                        }); //  was fade out to fade it in
                        if (totalRows > maxRows) {
                            // if tr total rows gt max rows option
                            var pagenum = Math.ceil(totalRows / maxRows); // ceil total(rows/maxrows) to get ..
                            //	numbers of pages
                            for (var i = 1; i <= pagenum; ) {
                            // for each page append pagination li
                            jQuery('.pagination #prev')
                                .before(
                                '<li data-page="' +
                                    i +
                                    '">\
                                                    <span>' +
                                    i++ +
                                    '<span class="sr-only"></span></span>\
                                                    </li>'
                                )
                                .show();
                            } // end for i
                        } // end if row count > max rows
                        jQuery('.pagination [data-page="1"]').addClass('active'); // add active class to the first li
                        jQuery('.pagination li').on('click', function(evt) {
                            // on click each page
                            evt.stopImmediatePropagation();
                            evt.preventDefault();
                            var pageNum = jQuery(this).attr('data-page'); // get it's number

                            var maxRows = parseInt(jQuery('#maxRows').val()); // get Max Rows from select option

                            if (pageNum == 'prev') {
                            if (lastPage == 1) {
                                return;
                            }
                            pageNum = --lastPage;
                            }
                            if (pageNum == 'next') {
                            if (lastPage == jQuery('.pagination li').length - 2) {
                                return;
                            }
                            pageNum = ++lastPage;
                            }

                            lastPage = pageNum;
                            var trIndex = 0; // reset tr counter
                            jQuery('.pagination li').removeClass('active'); // remove active class from all li
                            jQuery('.pagination [data-page="' + lastPage + '"]').addClass('active'); // add active class to the clicked
                            // jQuery(this).addClass('active');					// add active class to the clicked
                            limitPagging();
                            jQuery(table + ' tr:gt(0)').each(function() {
                            // each tr in table not the header
                            trIndex++; // tr index counter
                            // if tr index gt maxRows*pageNum or lt maxRows*pageNum-maxRows fade if out
                            if (
                                trIndex > maxRows * pageNum ||
                                trIndex <= maxRows * pageNum - maxRows
                            ) {
                                jQuery(this).hide();
                            } else {
                                jQuery(this).show();
                            } //else fade in
                            }); // end of for each tr in table
                        }); // end of on click pagination list
                        limitPagging();
                        })
                        .val(5)
                        .change();

                    // end of on select change

                    // END OF PAGINATION
                    }

                    function limitPagging(){
                        // alert(jQuery('.pagination li').length)

                        if(jQuery('.pagination li').length > 7 ){
                                if( jQuery('.pagination li.active').attr('data-page') <= 3 ){
                                jQuery('.pagination li:gt(5)').hide();
                                jQuery('.pagination li:lt(5)').show();
                                jQuery('.pagination [data-page="next"]').show();
                            }if (jQuery('.pagination li.active').attr('data-page') > 3){
                                jQuery('.pagination li:gt(0)').hide();
                                jQuery('.pagination [data-page="next"]').show();
                                for( let i = ( parseInt(jQuery('.pagination li.active').attr('data-page'))  -2 )  ; i <= ( parseInt(jQuery('.pagination li.active').attr('data-page'))  + 2 ) ; i++ ){
                                    jQuery('.pagination [data-page="'+i+'"]').show();

                                }

                            }
                        }
                    }

                    jQuery(function() {
                    // Just to append id number for each row
                    jQuery('table tr:eq(0)').prepend('<th> ID </th>');

                    var id = 0;

                    jQuery('table tr:gt(0)').each(function() {
                        id++;
                        jQuery(this).prepend('<td>' + id + '</td>');
                    });
                    });

                    //  Developed By Yasser Mas
                    // yasser.mas2@gmail.com

            </script>


        </div>
    </div>
</div>