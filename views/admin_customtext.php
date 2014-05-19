<?php 
    $add = add_query_arg(
        array_merge(
            $queryArgs,
            array(
                'action' => 'add'
            )
        ),
        $baseUrl
    );
 ?>
<div class="wrap">
    <div id="icon-options-general" class="icon32"><br /></div>
    <h2>Custom text <a href="<?php echo $add; ?>" class="add-new-h2">Add New</a></h2>
    <br/>


<table class="wp-list-table widefat fixed pages" style="width:100%">
    <thead>
    <tr>
        <th>S no.</th>
        <th>text</th>
        <th>Actions</th>
    </tr>
    </thead>
<?php
$count = 0;
if($customtexts):
foreach ($customtexts as $key => $customtext) {

    $edit = add_query_arg(
        array_merge(
            $queryArgs,
            array(
                'action' => 'edit',
                'id' => $key
            )
        ),
        $baseUrl
    );

    $delete = add_query_arg(
        array_merge(
            $queryArgs,
            array(
                'action' => 'delete',
                'id' => $key
            )
        ),
        $baseUrl
    );
    ?>
            <tr valign="top">
                <td><?php echo ++$count; ?></td>
                <td><?php echo $customtext['description']; ?></td>
                <td>
                    <a href="<?php echo $edit; ?>" class="button">Edit</a>
                    <a href="<?php echo $delete; ?>" class="button" onclick="return deleteConfirm();">Delete</a>
                </td>
            </tr>
    
    <?php
}
else:
?>
<tr valign="top">
    <td colspan="3">No custom texts are added yet.</td>
</tr>

<?php endif; ?>
<tfoot>
<tr>
    <th>S no.</th>
    <th class="row-title">text</th>
    <th>Actions</th>
</tr>
</tfoot>
        </table>
<script type="text/javascript">
    function deleteConfirm(){
        if(confirm("Are you sure you want to delete this custom text?")){
            return true;
        }else{
            return false;
        }
    }

</script>        