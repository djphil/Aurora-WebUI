    <TABLE cellSpacing=0 cellPadding=0 border=0>
        <TBODY>
            <TR>
                <TD vAlign=top align=right>
                    <TABLE cellSpacing=0 cellPadding=0 width=300 border=0>
                        <TBODY>
                            <tr>
                              <td class=gridbox_tl><img height=5 width=5 src="<?php echo SYSURL; ?>loginscreen/images/icons/spacer.gif" alt="" /></td>
                              <td class=gridbox_t><img height=5 width=5 src="<?php echo SYSURL; ?>loginscreen/images/icons/spacer.gif" alt="" /></td>
                              <td class=gridbox_tr><img height=5 width=5 src="<?php echo SYSURL; ?>loginscreen/images/icons/spacer.gif" alt="" /></td>
                            </tr>
                            <TR>
                                <TD class=gridbox_l></TD>
                                <TD class=black_content>
                                    <TABLE cellSpacing=0 cellPadding=1 width="100%" border=0>
                                        <TBODY>
                                            <TR>
                                                <TD class=gridtext align=left><STRONG><? echo $webui_grid_status; ?>:</STRONG></TD>
                                                <TD class=gridtext align=right>
                                                    <? if ($GRIDSTATUS == 1) { ?>
                                                    <SPAN class=ONLINE><? echo $webui_grid_status_online; ?></SPAN>
                                                    <? } else { ?>
                                                    <SPAN class=OFFLINE><? echo $webui_grid_status_offline; ?></SPAN>
                                                    <? } ?>
                                                </TD>
                                            </TR>
                                        </TBODY>
                                    </TABLE>

                                    <DIV id=GREX style="MARGIN: 1px 0px 0px">
                                        <IMG height=1 src="<?php echo SYSURL; ?>loginscreen/images/icons/spacer.gif" width=1>
                                    </DIV>

                                    <TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
                                        <TBODY>
                                            <TR class=odd>
                                                <TD class=gridtext vAlign=top noWrap align=left><? echo $webui_total_users ?>:</TD>
                                                <TD class=gridtext vAlign=top noWrap align=right width="1%"><?= $USERCOUNT ?></TD>
                                            </TR>
                                            <TR class=even>
                                                <TD class=gridtext vAlign=top noWrap align=left><? echo $webui_total_regions; ?>:</TD>
                                                <TD class=gridtext vAlign=top noWrap align=right width="1%"><?= $REGIONSCOUNT ?></TD>
                                            </TR>
                                            <TR class=odd>
                                                <TD class=gridtext vAlign=top noWrap align=left><? echo $webui_unique_visitors; ?>:</TD>
                                                <TD class=gridtext vAlign=top noWrap align=right width="1%"><?= $LASTMONTHONLINE ?></TD>
                                            </TR>
                                            <TR class=even>
                                                <TD class=gridtext vAlign=top noWrap align=left><STRONG><a href="<?php echo SYSURL; ?>index.php?page=onlineusers" target="_blank"><? echo $webui_online_now; ?></a>:</STRONG></TD>
                                                <TD class=gridtext vAlign=top noWrap align=right width="1%"><STRONG><?= $NOWONLINE ?></STRONG></TD>
                                            </TR>
                                        </TBODY>
                                    </TABLE>
                                </TD>
                                <TD class=gridbox_r></TD>
                            </TR>
                            <TR>
                              <TD class=gridbox_bl></TD>
                              <TD class=gridbox_b></TD>
                              <TD class=gridbox_br></TD>
                            </TR>
                        </TBODY>
                    </TABLE>
                </TD>
            </TR>
        </TBODY>
    </TABLE>
