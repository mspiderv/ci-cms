<table border="0" width="100%">
    <tr>
        <td style="text-align: center; color: #ff3300;">
            <h1 style="font-size: 7em;" class="jq-count"><?= $partners ?></h1>
            <p style="font-size: 2em;">Partnerov</p>
        </td>
        <td style="text-align: center; color: #5EAFCD;">
            <h1 style="font-size: 7em;" class="jq-count"><?= $campanings ?></h1>
            <p style="font-size: 2em;">Kampaní</p>
        </td>
        <td style="text-align: center; color: #5EAFCD;">
            <h1 style="font-size: 7em;" class="jq-count"><?= $advertisers ?></h1>
            <p style="font-size: 2em;">Inzerentov</p>
        </td>
    </tr>
</table>

<script>

    $('.jq-count').each(function(){
        var $self = $(this);
        var target = parseInt($self.text());
        var delay = 5;
        var delayFade = 1500;
        var i = 0;
        
        $self.parent().fadeTo(0, 0.3);
        
        function next()
        {
            $self.text(i++);
            if(i <= target)
                setTimeout(next, Math.round(delay / target));
            else
                $self.parent().fadeTo(delayFade, 1);
        }
        
        next();
        
    });

</script>