<form id="frmInstall" name="frmInstall" class="tabs-validation" novalidate>
	<br/><div class="row"><div class="col text-center"><?= $this->Html->image('logo.png',["alt" => 'Elucida']) ?></div></div>
	<div class="row mt-4">
		<div class="col d-flex justify-content-center">
			<div class="card w-75">
				<div class="card-header text-center">
					<h5 class="card-title">Instala&ccedil;&atilde;o do sistema</h5>
				</div>
				<div class="card-body">
					<p class="text-justify">Bem vindo ao instalador do PDV, neste ponto voc&ecirc; precisar&aacute; informar algumas configura&ccedil;&otilde;es b&aacute;sicas que s&atilde;o necess&aacute;rias antes que o sistema seja executado pela 1&ordf; vez.</p>
					<p class="text-justify">Para facilitar o entendimento, as informa&ccedil;&otilde;es est&atilde;o segmentadas abaixo.</p>

					<div class="row">
						<div class="col-sm-3">
							<div class="nav flex-column nav-pills" id="v-pills-tab" role="tablist" aria-orientation="vertical">
								<a class="nav-link active" id="v-pills-home-tab" data-toggle="pill" href="#v-pills-home" role="tab" aria-controls="v-pills-home" aria-selected="true">Acesso</a>
								<a class="nav-link" id="v-pills-system-tab" data-toggle="pill" href="#v-pills-system" role="tab" aria-controls="v-pills-system" aria-selected="false">Sistema</a>
								<a class="nav-link" id="v-pills-suprimento-tab" data-toggle="pill" href="#v-pills-suprimento" role="tab" aria-controls="v-pills-suprimento" aria-selected="false">Suprimentos</a>
								<a class="nav-link" id="v-pills-comercial-tab" data-toggle="pill" href="#v-pills-comercial" role="tab" aria-controls="v-pills-comercial" aria-selected="false">Comercial</a>
                                <a class="nav-link" id="v-pills-financeiro-tab" data-toggle="pill" href="#v-pills-financeiro" role="tab" aria-controls="v-pills-financeiro" aria-selected="false">Financeiro</a>
								<a class="nav-link" id="v-pills-contact-tab" data-toggle="pill" href="#v-pills-contact" role="tab" aria-controls="v-pills-contact" aria-selected="false">Comunica&ccedil;&atilde;o</a>
							</div>
						</div>
						<div class="col-sm-9">
							<div class="tab-content" id="v-pills-tabContent">
								<div class="tab-pane fade show active" id="v-pills-home" role="tabpanel" aria-labelledby="v-pills-home-tab">
									<div class="form-group">
										<label>Nome:</label>
										<input type="text" id="txtUserName" name="txtUserName" placeholder="Nome do usu&aacute;rio administrador" class="form-control form-control-sm" required/>
									</div>
                                    <div class="form-group">
                                        <label>Login:</label>
                                        <input type="email" id="txtUserLogin" name="txtUserLogin" placeholder="E-mail para login" class="form-control form-control-sm" required/>
                                    </div>
									<div class="form-row">
										<div class="col-sm">
											<label>Senha:</label>
											<input type="password" id="txtUserSenha" name="txtUserSenha" class="form-control form-control-sm" required/>
										</div>
										<div class="col-sm">
											<label>Confirma&ccedil;&atilde;o de Senha:</label>
											<input type="password" id="txtUserCsenha" name="txtUserCsenha" class="form-control form-control-sm" required/>
										</div>
									</div>
								</div>
								<div class="tab-pane fade" id="v-pills-system" role="tabpanel" aria-labelledby="v-pills-system-tab">
									<div class="form-row">
										<div class="form-group col-sm">
											<label>Empresa:</label>
											<input type="text" id="txtCompanyName" name="txtCompanyName" placeholder="Nome da Empresa" class="form-control form-control-sm" required/>
										</div>
										<div class="form-group col-sm">
											<label>Loja Padr&atilde;o do Sistema:</label>
											<input type="text" id="txtStoreDefault" name="txtStoreDefault" placeholder="Nome da loja. Ex: Matriz" class="form-control form-control-sm" required/>
										</div>
									</div>
									<fieldset>
										<legend>Agendamentos</legend>
										<div class="form-row">
											<div class="form-group col-sm">
												<label>Fluxo de Caixa</label>
												<input type="time" id="txtHoraFluxoCaixa" name="txtHoraFluxoCaixa" class="form-control form-control-sm" required/>
											</div>
											<div class="form-group col-sm">
												<label>Aniversariante m&ecirc;s</label>
												<input type="time" id="txtHoraAniversariante" name="txtHoraAniversariante" class="form-control form-control-sm" required/>
											</div>
											<div class="form-group col-sm">
												<label>Transfer&ecirc;cias</label>
												<input type="time" id="txtHoraTransferencia" name="txtHoraTransferencia" class="form-control form-control-sm" required/>
											</div>
										</div>
										<div class="form-row">
											<div class="form-group col-sm">
												<label>Anivers&aacute;rio da Loja</label>
												<input type="time" id="txtHoraNiverLoja" name="txtHoraNiverLoja" class="form-control form-control-sm" required/>
											</div>
											<div class="form-group col-sm">
												<label>M&ecirc;s do anivers&aacute;rio da empresa</label>
												<select class="form-control form-control-sm" id="cbMesAniversario" name="cbMesAniversario" required>
                                                    <option value="">&laquo; Selecione &raquo;</option>
													<?php for($i=1;$i<13;$i++):?>
													<option value="<?=str_pad($i,2,"0",STR_PAD_LEFT); ?>"><?=str_pad($i,2,"0",STR_PAD_LEFT); ?></option>
													<?php endfor; ?>
												</select>
											</div>
										</div>
									</fieldset>
								</div>
								<div class="tab-pane fade" id="v-pills-suprimento" role="tabpanel" aria-labelledby="v-pills-suprimento-tab">
									<div class="form-row">
										<div class="form-group col-sm">
											<label>Tipo de Produto Padr&atilde;o:</label>
											<input type="text" id="txtTipoProdutoPadrao" name="txtTipoProdutoPadrao" placeholder="Nome do tipo de produto" class="form-control form-control-sm" required/>
										</div>
										<div class="form-group col-sm">
											<label>Tamanho do C&oacute;d. Barras:</label>
											<input type="number" id="txtBarcodeSize" name="txtBarcodeSize" placeholder="Ex: 13" class="form-control form-control-sm" required/>
										</div>
										<div class="form-group col-sm">
											<label>Prazo da(s) transfer&ecirc;ncias</label>
											<input type="number" id="txtPrazoTransferencia" name="txtPrazoTransferencia" placeholder="Ex: 3" class="form-control form-control-sm" required/>
										</div>
									</div>
								</div>
								<div class="tab-pane fade" id="v-pills-comercial" role="tabpanel" aria-labelledby="v-pills-comercial-tab">
									<div class="form-row">
                                        <div class="form-group col">
                                            <label>Exigir cliente na compra</label>
                                            <select class="form-control form-control-sm" id="cbExigeCliente" name="cbExigeCliente" required>
                                                <option value="">&laquo; Selecione &raquo;</option>
                                                <option value="0">N&atilde;o</option>
                                                <option value="1">Sim</option>
                                            </select>
                                        </div>
                                        <div class="form-group col">
                                            <label>Cargo do Vendedor</label>
                                            <input type="text" id="txtCargoVendedor" name="txtCargoVendedor" class="form-control form-control-sm" required placeholder="Nome do cargo">
                                        </div>
                                    </div>
								</div>
                                <div class="tab-pane fade" id="v-pills-financeiro" role="tabpanel" aria-labelledby="v-pills-financeiro-tab">
                                    <div class="form-group">
                                        <label>Primeira hora calend&aacute;rio</label>
                                        <input type="number" min="0" max="12" id="txtFirstHourCalendar" name="txtFirstHourCalendar" class="form-control form-control-sm" required>
                                    </div>
                                </div>
								<div class="tab-pane fade" id="v-pills-contact" role="tabpanel" aria-labelledby="v-pills-contact-tab">
									<fieldset>
										<legend>E-mail padr&atilde;o</legend>
										<div class="form-row">
											<div class="form-group col-sm">
												<label>Servidor:</label>
												<input type="text" id="txtEmailServer" name="txtEmailServer" class="form-control form-control-sm" placeholder="Ex: localhost" required/>
											</div>
											<div class="form-group col-sm-2">
												<label>Porta:</label>
												<input type="number" id="txtEmailPort" name="txtEmailPort" class="form-control form-control-sm" required/>
											</div>
											<div class="form-group col-sm">
												<label>Login:</label>
												<input type="email" id="txtEmailLogin" name="txtEmailLogin" class="form-control form-control-sm" placeholder="Ex: usuario@localhost" required/>
											</div>
											<div class="form-group col-sm">
												<label>Senha:</label>
												<input type="password" id="txtEmailSenha" name="txtEmailSenha" class="form-control form-control-sm" required/>
											</div>
										</div>
										<div class="form-row">
											<div class="form-group col-sm">
												<label>E-mail Padr&atilde;o:</label>
												<input type="email" id="txtEmailEmail" name="txtEmailEmail" class="form-control form-control-sm" placeholder="Ex: usuario@nomedaempresa.com.br" required/>
											</div>
											<div class="form-group col-sm">
												<label>Nome do E-mail Padr&atilde;o:</label>
												<input type="text" id="txtEmailNome" name="txtEmailNome" class="form-control form-control-sm" placeholder="Ex: Nome do Usu&aacute;rio" required/>
											</div>
										</div>
										<div class="form-row">
											<div class="form-group col-sm">
												<label>% Desconto Aniversariante:</label>
												<input type="number" id="txtDescontoNiver" name="txtDescontoNiver" class="form-control form-control-sm" required/>
											</div>
											<div class="form-group col-sm">
												<label>% Desconto Anivers&aacute;rio Loja:</label>
												<input type="number" id="txtDescontoNiverLoja" name="txtDescontoNiverLoja" class="form-control form-control-sm" required/>
											</div>
										</div>
									</fieldset>
								</div>
							</div>
						</div>
					</div>
				</div>
                <div class="card-footer text-right">
                    <button type="submit" class="btn btn-primary btn-sm">Salvar e continuar</button>
                </div>
			</div>
		</div>
	</div><br/>
</form>
<script>
function checkAba(){
    if($("#v-pills-home-tab").hasClass("active")){
        if($("#txtUserName").val()!=""){
			if($("#txtUserLogin").val()!=""){
				if($("#txtUserSenha").val()!=""){
					if($("#txtUserCsenha").val()!=""){
						if($("#txtUserSenha").val()==$("#txtUserCsenha").val()){
							$("#v-pills-system-tab").trigger("click");
							return false;
						}else{
							bootbox.alert("Por favor confirme corretamente a senha");
							return false;
						}
					}
				}
			}
        }
    }

    if($("#v-pills-system-tab").hasClass("active")){
        if($("#txtCompanyName").val()!=""){
            if($("#txtStoreDefault").val()!=""){
                if($("#txtHoraFluxoCaixa").val()!=""){
                    if($("#txtHoraAniversariante").val()!=""){
                        if($("#txtHoraTransferencia").val()!=""){
                            if($("#txtHoraNiverLoja").val()!=""){
                                if($("#cbMesAniversario").val()!=""){
                                    $("#v-pills-suprimento-tab").trigger("click");
                                    return false;
                                }
                            }
                        }
                    }
                }
            }
        }
    }

    if($("#v-pills-suprimento-tab").hasClass("active")){
        if($("#txtTipoProdutoPadrao").val()!=""){
            if($("#txtBarcodeSize").val()!=""){
                if($("#txtPrazoTransferencia").val()!=""){
                    $("#v-pills-comercial-tab").trigger("click");
                    return false;
                }
            }
        }
    }

    if($("#v-pills-comercial-tab").hasClass("active")){
        if($("#cbExigeCliente").val()!=""){
            if($("#txtCargoVendedor").val()!=""){
                $("#v-pills-financeiro-tab").trigger("click");
                return false;
            }
        }
    }

    if($("#v-pills-financeiro-tab").hasClass("active")){
        if($("#txtFirstHourCalendar").val()!=""){
            $("#v-pills-contact-tab").trigger("click");
            return false;
        }
    }

    if($("#v-pills-contact").hasClass("active")){
        if($("#txtEmailServer").val()!=""){
			if($("#txtEmailPort").val()!=""){
				if($("#txtEmailLogin").val()!=""){
					if($("#txtEmailSenha").val()!=""){
						if($("#txtEmailEmail").val()!=""){
							if($("#txtEmailNome").val()){
								if($("#txtDescontoNiver").val()!=""){
									if($("#txtDescontoNiverLoja").val()!=""){
										return true;
									}
								}
							}
						}
					}
				}
			}
		}
    }
    return true;
}
$(document).on("submit","#frmInstall",function(event){
    event.preventDefault();

	var dataForm = {
		USER_NAME           : $("#txtUserName").val(),
		USER_LOGIN          : $("#txtUserLogin").val(),
		USER_SENHA          : $("#txtUserSenha").val(),

		SYS_COMPANY_NAME    : $("#txtCompanyName").val(),
		SYS_STORE_DEFAULT   : $("#txtStoreDefault").val(),
		SYS_HORA_FLUXO      : $("#txtHoraFluxoCaixa").val(),
		SYS_HORA_NIVER      : $("#txtHoraAniversariante").val(),
		SYS_HORA_TRANSFER   : $("#txtHoraTransferencia").val(),
		SYS_HORA_NIVER_LOJA : $("#txtHoraNiverLoja").val(),
		SYS_MES_NIVER_LOJA  : $("#cbMesAniversario").val(),

		SUP_TIPO_PRODUTO    : $("#txtTipoProdutoPadrao").val(),
		SUP_BARCODE_SIZE    : $("#txtBarcodeSize").val(),
		SUP_PRAZO_TRANSFER  : $("#txtPrazoTransferencia").val(),

		COM_EXIGE_CLIENTE   : $("#cbExigeCliente").val(),
		COM_CARGO_VENDEDOR  : $("#txtCargoVendedor").val(),

		FIN_FIRST_HOUR      : $("#txtFirstHourCalendar").val(),

		COM_MAIL_SERVER     : $("#txtEmailServer").val(),
		COM_MAIL_PORT       : $("#txtEmailPort").val(),
		COM_MAIL_LOGIN      : $("#txtEmailLogin").val(),
		COM_MAIL_SENHA      : $("#txtEmailSenha").val(),
		COM_MAIL_MAIL       : $("#txtEmailEmail").val(),
		COM_MAIL_NAME       : $("#txtEmailNome").val(),
		COM_DESC_NIVER      : $("#txtDescontoNiver").val(),
		COM_DESC_NIVER_LOJA : $("#txtDescontoNiverLoja").val()
	};

	$.ajax({
		headers:{
			'X-CSRF-Token':csrf
		},
		type: "post",
		url: "<?=$this->Url->build('/install/save')?>",
		data: dataForm,
		success: function (response) {
			if(response){
				bootbox.alert("Instala&ccedil&atilde;o b&aacute;sica realizada com sucesso! Acesse o sistema pela primeira vez e conclua as configura&ccedil;&otilde;es.",function(){ document.location.href="<?=$this->Url->build("/system/")?>"; });
			}else{
				bootbox.alert("Ocorrreu um erro ao tentar salvas os dados da instala&ccedil;&atilde;o b&aacute;sica, por favor entre em contato com o fornecedor.<br/>Obrigado!");
			}
		}
	});
});
</script>







