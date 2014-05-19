<div class="wrap">
    <div id="icon-options-general" class="icon32"><br /></div>
    <h2>Twitter Settings</h2>
<br/>

<form action="" method="post">
    <table>
    <tr>
        <td width="190"> Twitter profile:</td> 
        <td><input type="text" name="twitter_profile" id="twitter_profile" rows="10" cols="90" value="<?php echo $result['twitter_profile']; ?>"/></td>
    </tr>
    <tr>
        <td width="190"> Twitter consumer key:</td> 
        <td><input type="text" name="twitter_consumer_key" id="twitter_consumer_key" rows="10" cols="90" value="<?php echo $result['twitter_consumer_key']; ?>"/></td>
    </tr>

    <tr>
        <td width="190">Twitter consumer secret:</td> 
        <td><input type="text" name="twitter_consumer_secret" id="twitter_consumer_secret" rows="10" cols="90" value="<?php echo $result['twitter_consumer_secret']; ?>"/></td>
    </tr>

    <tr>
        <td width="190">Twitter access token:</td> 
        <td><input type="text" name="twitter_access_token" id="twitter_access_token" rows="10" cols="90" value="<?php echo $result['twitter_access_token']; ?>"/></td>
    </tr>

    <tr>
        <td width="190">Twitter access token secret:</td> 
        <td><input type="text" name="twitter_access_token_secret" id="twitter_access_token_secret" rows="10" cols="90" value="<?php echo $result['twitter_access_token_secret']; ?>"/></td>
    </tr>

    <tr>
        <td width="190" style="text-align:left;">
            <input type="hidden" name="action" value="addtwitter">
            <input type="submit" class="button button-primary button-large" name="submit" value="Submit">
        </td>     
    </tr>
    </table>
</form>
</div>