<?php
if ($editCustomtext !== false) {
    $description = $customtext[$editCustomtext]['description'];
} else {
    $description = '';
}
?>

<div class="wrap">
    <div id="icon-options-general" class="icon32"><br /></div>
    <h2><?php echo (isset($editCustomtext))?'Edit':'Add' ?> Custom text</h2>
<br/>

<form action="" method="post">
    <table>
    <tr>
        <td width="120">Text:</td> 

        <td>

        <textarea name="description" rows="10" cols="35"><?php echo $description; ?></textarea></td>
    </tr>
   
    <tr>
        <td width="120" style="text-align:left;">
            <?php if(isset($editCustomtext)): ?>
            <input type="hidden" name="action" value="editcustomtext">
            <input type="hidden" name="id" value="<?php echo $editCustomtext; ?>">
            <?php else: ?>
            <input type="hidden" name="action" value="addcustomtext">
            <?php endif; ?>    
            <input type="submit" class="button button-primary button-large" name="submit" value="Submit">
        </td>     
    </tr>
    </table>
</form>
</div>  
