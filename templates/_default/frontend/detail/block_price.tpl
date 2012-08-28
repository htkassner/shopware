{block name='frontend_detail_data_block_prices_start'}
    <div class="block-prices {$sArticle.ordernumber}{if $hidden && !$sArticle.selected} hidden{/if}">
        <div class="space">&nbsp;</div>
        <h5 class="bold">{se namespace="frontend/detail/data" name="DetailDataHeaderBlockprices"}{/se}</h5>

        <table width="220"  border="0" cellspacing="0" cellpadding="0" class="text">
            <thead>
                <tr>
                    <td width="90">
                        <strong>{se namespace="frontend/detail/data" name="DetailDataColumnQuantity"}{/se}</strong>
                    </td>
                    <td width='70'>
                        <strong>{se namespace="frontend/detail/data" name="DetailDataColumnPrice"}{/se}</strong>
                    </td>
                </tr>
            </thead>

            <tbody>
                {foreach from=$sArticle.sBlockPrices item=row key=key}
                    {block name='frontend_detail_data_block_prices'}
                    <tr valign="top">
                        <td>
                            {if $row.from=="1"}
                                {se namespace="frontend/detail/data" name="DetailDataInfoUntil"}{/se} {$row.to}
                            {else}
                                {se namespace="frontend/detail/data" name="DetailDataInfoFrom"}{/se} {$row.from}
                            {/if}
                        </td>
                        <td>
                            <strong>
                                {$row.price|currency}*
                            </strong>
                        </td>
                    </tr>
                    {/block}
                {/foreach}
            </tbody>
        </table>
    </div>
{/block}