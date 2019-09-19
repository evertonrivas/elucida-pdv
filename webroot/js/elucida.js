/**
* 
* @var int selectedImageID recebera o ID da imagem selecionada
* 
*/
var selectedImageID   = 0;

/**
* 
* @var string url_filter receberah a string da url de filtros
* 
*/
var url_filter = null;

/**
* 
* @var string url_data receberah a string da url de dados
* 
*/
var url_data = null;

/**
* 
* @var string selectedImagePath receberah o caminho da imagem selecionada
* 
*/
var selectedImagePath = null;

/**
* Necessario para fechar o modal de galeria
* 
*/
window.closeModalGallery = function(){
    $('#modalGallery').modal('hide');
};

/**
* Aqui estao as cores que serao utilizadas nos graficos do dashboard
* @var barsColors 
* 
*/
barsColors = {
	red: 'rgba(255, 99, 132, 0.5)',
	pink: 'rgba(255,192,203,0.5)',
	orange: 'rgba(255, 159, 64, 0.5)',
	yellow: 'rgba(255, 205, 86, 0.5)',
	green: 'rgba(75, 192, 192, 0.5)',
	blue: 'rgba(54, 162, 235, 0.5)',
	lightBlue: 'rgba(0, 191, 255,0.5)',
	purple: 'rgba(153, 102, 255, 0.5)',
	brown: 'rgba(160, 82, 45,0.5)',
	grey: 'rgba(201, 203, 207, 0.5)',
	black: 'rgba(0, 0, 0, 0.5)',
	teal: 'rgba(0, 128, 128, 0.5)'
};
/**
* Aqui estao as cores das bordas que serao utilizadas nos graficos do dashboard
* @var chartColors
* 
*/
chartColors = {
	red: 'rgb(255, 99, 132)',
	pink: 'rgb(255,192,203)',
	orange: 'rgb(255, 159, 64)',
	yellow: 'rgb(255, 205, 86)',
	green: 'rgb(75, 192, 192)',
	blue: 'rgb(54, 162, 235)',
	lightBlue: 'rgb(0, 191, 255)',
	purple: 'rgb(153, 102, 255)',
	brown: 'rgb(160, 82, 45)',
	grey: 'rgb(201, 203, 207)',
	black: 'rgb(0, 0, 0)',
	teal: 'rgb(0, 128, 128)'
};

/**
* Ao terminar de ler a pagina formata os campos data e inicia o tooltip
* 
*/
$(document).ready(function(){
	$('.date').mask('00/00/0000');
	$('[data-toggle="tooltip"]').tooltip();
	
	//cria o div de propagandas
	/*var dvAds = document.createElement("div");
	dvAds.setAttribute("id","teste");
	dvAds.setAttribute("style","background:#CCC;width:310px;height:260px;position:absolute;left:0;bottom:0;z-index:200000");
	
	var frmAd = document.createElement("iframe");
	frmAd.setAttribute("src","http://elucida.com.br/openAd");
	frmAd.setAttribute("style","width:300px;height:250px;overflow:hidden;border:0");
	
	dvAds.appendChild(frmAd);
	
	document.getElementsByTagName("body")[0].appendChild(dvAds);*/
});

/**
* Realiza a formacao dos campos tipo data ao finalizar o ajax
*/
$(document).ajaxComplete(function(event, xhr, settings){
    $('.date').mask('00/00/0000');
    $('[data-toggle="tooltip"]').tooltip();
});


$(document).on("show.bs.modal","#modalFilter",function(event, xhr, settings){
    $('.date').mask('00/00/0000');
});


/**
* Evento que detecta a exibicao do modal de galeria e chama
* o conteudo do mesmo
* 
*/
$(document).on("shown.bs.modal","#modalGallery",function(){
	$.ajax({
		headers:{
			'X-CSRF-Token' :csrf
		},
		url: '/gallery/',
		success: function(data){
			$("#modalGalleryBody").html(data);
		}
	})
});

/**
* Evento que detecta a exibicao do modal de filtragem e chama
* o conteudo do mesmo
*/
$(document).on("show.bs.modal","#modalFilter",function(){
	$.ajax({
		headers: {
			'X-CSRF-Token' : csrf
		},
        url: url_filter,
        success:function(data){
            $("#filters").html(data);
        }
    });
});

/**
* Funcao do bootstrap que realiza a validacao de campos  
* 
*/
(function() {
  'use strict';
  window.addEventListener('load', function() {
    // Fetch all the forms we want to apply custom Bootstrap validation styles to
    var forms = document.getElementsByClassName('needs-validation');
    // Loop over them and prevent submission
    var validation = Array.prototype.filter.call(forms, function(form) {
      form.addEventListener('submit', function(event) {
        if (form.checkValidity() === false) {
          event.preventDefault();
          event.stopPropagation();
        }
        form.classList.add('was-validated');
      }, false);
    });
  }, false);
})();

/**
* Funcao do bootstrap que realiza a validacao de campos com abas
* 
*/
(function() {
  'use strict';
  window.addEventListener('load', function() {
    // Fetch all the forms we want to apply custom Bootstrap validation styles to
    var forms = document.getElementsByClassName('tabs-validation');
    // Loop over them and prevent submission
    var validation = Array.prototype.filter.call(forms, function(form) {
      form.addEventListener('submit', function(event) {
      	if(checkAba()===false){
			event.preventDefault();
			event.stopPropagation();
		}
      	
        if (form.checkValidity() === false) {          
          event.preventDefault();
          event.stopPropagation();
        }
        form.classList.add('was-validated');
      }, false);
    });
  }, false);
})();

/**
* Funcao que busca os dados de registro na tela principal de cada cadastro
* @param string url caminho que vem do PHP
* 
* @return null
*/
function getData(){
    $.ajax({
		headers: {
			'X-CSRF-Token': csrf
		},
        method:'post',
        data: $("#frmFilter").serialize(),
        url:url_data,
        success:function(data){
            $("#divResult").html(data);
        }
    });
}

/**
* Evento que limpa as informacoes de filtro nas paginas de litagem de dados
* e remete o formulario de busca
* 
* @return null
*/
$(document).on("click","#btnResetFilter",function(){
	$("#LNK_DATA").val("");
	$('#frmFilter .btn-group').find("label").removeClass('active').end().find('[type="radio"]').prop("checked",false);
	$("#frmFilter")[0].reset();
	$("#frmFilter").submit();
});

/**
* Evento de clique em paginacao que faz busca o restante dos dados da proxima pagina
*/
$(document).on("click",".pagination a",function(){
	url_data = $(this).prop("href");
    getData();
    return false;
});

/**
* Funcao que coloca zero a esquerda de um numero
* @param int number - numero que recebera o zero
* @param int width - tamanho do numero
* 
* @return int - numero formatado
*/
function zeroFill( number, width ){
  width -= number.toString().length;
  if ( width > 0 ){
    return new Array( width + (/\./.test( number ) ? 2 : 1) ).join( '0' ) + number;
  }
  return number + ""; // always return a string
}

/**
* Evento de clique no checkbox de marcar todos que ha em cada pagina de listagem
*/
$(document).on("click","#chCheckAll",function(){
    if($("#chCheckAll")[0].checked){
        $("input[name='check_list[]']").each(function(){
            if(!$(this).prop("disabled"))
                $(this)[0].checked = true;
        });
    }
    else
    {
        $("input[name='check_list[]']").each(function(){
            $(this)[0].checked = false;
        });
    }
});

/**
* Funcao que chama o metodo de exclusao de registro
* @param string url_delete caminho do metodo PHP que realiza a exclusao
* 
* @return null
*/
function trash(url_delete){
	var totalChecked = 0;
    $("input[name='check_list[]']").each(function(){
        if( $(this)[0].checked ==true ){
            totalChecked = totalChecked + 1;
        }
    });
    
    if(totalChecked>0){
        bootbox.dialog({message:"Deseja realmente excluir o(s) registro(s) selecionado(s)?", 
            buttons:{
                yes:{
                    label:"Sim",
                    callback:function(){
                        $.ajax({
							headers: {
								'X-CSRF-Token': csrf
							},
                            method:'post',
                            url: url_delete,
                            data: $("#frmRegs").serialize(),
                            success: function(data){
                                if(data==true){
                                    bootbox.alert('Registro(s) exclu&iacute;do(s) com sucesso!',function(){ getData(); });
                                }else{
									bootbox.alert('Ocorreu um erro ao tentar excluir o(s) registro(s) selecionados!');
                                }
                            }	
                        });
                    }
                },
                no:{
                    label:"N\u00e3o"			
                }
            }
        });
    }
    else{
        bootbox.alert("Selecione ao menos um registro para excluir!");
    }
}

/**
* Funcao que mostra o modal de ajuda e abre o arquivo de help
* @param String ID codigo da dajuda
* 
* @return null
*/
function help(ID){
	$("#modalHelp").modal({
		backdrop:'static'
	});
}

/**
* Evento de envio do formulario denominado frmFindCity que encontra-se no
* dialog de busca de cidades (ver DialogHelper.php na pasta src/View/Helper/)
*/
$(document).on("submit","#frmFindCity",function(e){
    e.preventDefault();
    
    var dataForm = {
        CODIBGE : $("#txtCityCodIBGE").val(),
        NOME    : $("#txtCityNome").val(),
        UF      : $("#txtCityProvince").val()
    };
    
    $.ajax({
		headers: {
			'X-CSRF-Token': csrf
		},
        method:'post',
        data: dataForm,
        url:'/system/city_find/',
        success:function(data){
            $("#tblResultCity tbody").html(data);
       }
    });
});

/**
* Evento de envio do formulario denominado frmFindBank que encontra-se no
* dialog de busca de bancos (ver DialogHelper.php na pasta src/View/Helper/)
*/
$(document).on("submit","#frmFindBank",function(e){
    e.preventDefault();
    
    var dataForm = {
        CODFEBRABAN : $("#txtBankCodFebraban").val(),
        NOME        : $("#txtBankNome").val()
    };
    
    $.ajax({
		headers: {
			'X-CSRF-Token': csrf
		},
        method:'post',
        data:dataForm,
        url:'/system/bank_find/',
        success:function(data){
            $("#tblResultBank tbody").html(data);
        }
    });
});

/**
* Funcao que seleciona a imagem que estah dentro de um iframe no modal
* joga para a janela anteior o ID e o caminho atraves dos metodos
* window.parent.selectedImageID e window.parent.selectedImagePath
* em seguida fecha o modal com a funcao window.parent.closeModal()
* @param {object} idImagem
* 
* @return null
*/
function selectImage(idImagem){
	$.ajax({
		headers:{
			'X-CSRF-Token' : csrf
		},
		method: 'post',
		url: '/gallery/image/'+idImagem,
		dataType: 'json',
		success: function(data){
			window.parent.selectedImageID   = data.IDMAGEM;
			window.parent.selectedImagePath = '/img/gallery/'+data.IDALBUM+'/'+data.ARQUIVO;
			window.parent.closeModalGallery();
		}
	});
}

/**
* Evento de envio do formulario denominado frmProductDialogMultiple
* que realiza busca de produtos simples para serem utilizados no sistema
* ver DialogHelper.php na pasta src/View/Helper/
*/
$(document).on("submit","#frmProductDialogMultiple",function(e){
    e.preventDefault();
    
    var dataForm = {
        IDLOJA   : $("#TXT_PRODUCTM_DIALOG_STORE").val(),
        SKU      : $("#TXT_PRODUCTM_DIALOG_SEARCH_SKU").val(),
        NOME     : $("#TXT_PRODUCTM_DIALOG_SEARCH_NAME").val()
    };
    
    if($("#TXT_PRODUCTM_DIALOG_SEARCH_SKU").val()!="" || $("#TXT_PRODUCTM_DIALOG_SEARCH_NAME").val()!=""){
    $.ajax({
    	headers:{
			'X-CSRF-Token':csrf
		},
        method:'post',
        data:dataForm,
        url: '/stock/product_dialog/product_dialog_multiple',
        success:function(data){
            $("#tableSearchProductMultiple tbody").html(data);
        }
    });
    }else{
        bootbox.alert('Por favor filtre a informa&ccedil;&atilde;o para consultar!');
    }
});

/**
* Evento de envio do formulario denominado frmProductDialogSingle
* que realiza busca de produto para ser utilizado no sistema
* ver DialogHelper.php na pasta src/View/Helper/
*/
$(document).on("submit","#frmProductDialogSingle",function(e){
    e.preventDefault();
    
    var dataForm = {
        IDLOJA   : $("#TXT_PRODUCTS_DIALOG_STORE").val(),
        SKU      : $("#TXT_PRODUCTS_DIALOG_SEARCH_SKU").val(),
        NOME     : $("#TXT_PRODUCTS_DIALOG_SEARCH_NAME").val()
    };
    
    if($("#TXT_PRODUCTS_DIALOG_SEARCH_SKU").val()!="" || $("#TXT_PRODUCTS_DIALOG_SEARCH_NAME").val()!=""){
    $.ajax({
    	headers:{
			'X-CSRF-Token':csrf
		},
        method:'post',
        data:dataForm,
        url:'/stock/product_dialog/',
        success:function(data){
            $("#tableSearchProductSingle tbody").html(data);
        }
    });
    }else{
        bootbox.alert('Por favor filtre a informa&ccedil;&atilde;o para consultar!');
    }
});

/**
* Evento que detecta clique no btnUseSingleProduct que estah no productDialog
* Quado o usuario selecionar um produto ele irah:
* - Fechar o modal
* - Limpar os campos de busca
* - chamar a funcao getProductInfo que faz uma busca no banco de dados 
*/
$(document).on("click","#btnUseSingleProduct",function(){
	
    $("input[name='rdProduto[]']").each(function(){
        if($(this)[0].checked){
            getProductInfo($(this).val(),"ID",$("#TXT_PRODUCTS_DIALOG_STORE").val());
            $("#modalProduto").modal('hide');
            
            //limpa o dialog de busca
            $("#tblFindProduct tbody").html("");
            $("#txtNomeBuscaProduto").val("");
            $("#txtSKUBuscaProduto").val("");
            $("#CB_PRODUCT_DIALOG_SEARCH_COND").val("");
            $("#TXT_PRODUCT_DIALOG_SEARCH_QTDE").val("");
        }
    });
});


/**
* Funcao que busca informacoes de um produto
* a funcao chama uma outra funcao denominada useProduct que precisa ser declarada
* na pagina onde deseja utilizar as informacoes
* @param string id IDPRODUTO ou CODIGO_BARRA
* @param string tipo ID ou BARCODE
* @param int loja codigo da Loja
* 
* @return obj
*/
function getProductInfo(id,tipo,loja){
	
    $.ajax({
    	headers:{
			'X-CSRF-Token':csrf	
		},
        url: '/stock/product_get_info/'+id+'/'+tipo+'/'+loja,
        success: function(result){
            var obj = $.parseJSON( result );
            if(obj.NOME!=null){
            	useProduct(obj);
            }
            else{
                bootbox.alert("Produto inexistente no sistema, por favor verifique o c&oacute;digo de barras!");
            }
        }
    });
}

/**
* Evento que avalia o envio das informacoes de filtragem
*/
$(document).on("submit","#frmFilter",function(e){
    e.preventDefault();
    
    $("#modalFilter").modal('hide');
    
    getData();
});

/**
* Evento que realiza a busca de clientes no dialog
*/
$(document).on("submit","#frmFindCliente",function(event){
    event.preventDefault();

    if($("#TXT_CUSTOMER_SEARCH_ID").val()!="" || $("#TXT_CUSTOMER_SEARCH_TAXVAT").val()!="" || $("#TXT_CUSTOMER_SEARCH_NAME").val()!="" || $("#TXT_CUSTOMER_SEARCH_PHONE").val()!=""){
        var dataForm = {
        	TXT_CUSTOMER_SEARCH_ID    : $("#TXT_CUSTOMER_SEARCH_ID").val(),
            TXT_CUSTOMER_SEARCH_TAXVAT : $("#TXT_CUSTOMER_SEARCH_TAXVAT").val(),
            TXT_CUSTOMER_SEARCH_NAME   : $("#TXT_CUSTOMER_SEARCH_NAME").val(),
            TXT_CUSTOMER_SEARCH_PHONE  : $("#TXT_CUSTOMER_SEARCH_PHONE").val()
        };

        $.ajax({
        	headers:{
				'X-CSRF-Token':csrf
			},
            method: 'post',
            url: '/retail/customer_dialog',
            data: dataForm,
            success: function(data){
                $("#tblFindCustomer tbody").html(data);
            }
        });
    }
    else{
        $("#tblFindCustomer tbody").html("");
    }
});

/**
* Evento que detecta o clique no botao de usar cliente selecionado
*/
$(document).on("click","#btnUseCustomer",function(){
    $("input[name='rdCliente[]']").each(function(){
        if($(this)[0].checked){
            useCustomer( $(this).val() );
            $("#modalFindCliente").modal('hide');
        }
    });
});

/**
* Funcao que abre o modal para cadastro de novo cliente
* 
* @return null
*/
function modalNewCustomer(){
    $("#modalNewCustomer").modal({
        backdrop:'static'
    });
    
    $("#modalFindCliente").modal('hide');
}

function openCashFlow(){
	$("#txtPathToCashFlow").val(pathSystem+"report/cash_flow/");
	$("#modalCashFlowTitle").html("Exibi&ccedil;&atilde;o do Fluxo de Caixa");
	
	$("#modalCashFlow").modal({
		backdrop:'static'
	});
}

function updateCashFlow(rebuild){
	$("#txtPathToCashFlow").val(pathSystem+"system/update_cash_flow/");
	
    if(rebuild){
        $("#dateToUpdate").val('2014-06-01');
        $("#modalCashFlowTitle").html('Reconstru&ccedil;&atilde;o de Fluxo de Caixa');
    }else{
        $("#dateToUpdate").val('');
        $("#modalCashFlowTitle").html('Atualiza&ccedil;&atilde;o de Fluxo de Caixa');
    }

    $("#modalCashFlow").modal({
        backdrop: 'static'
    });
}

$(document).on("show.bs.modal","#modalCashFlow",function(){
	
	var url = $("#txtPathToCashFlow").val();
	if($("#dateToUpdate").val()!=""){
		url = url + '/'+$("#dateToUpdate").val();
	}
	
	$("#frmCashflow").attr("src",url);
});

/**
* Evento que realiza o cadastro do cliente no banco de dados
*/
$(document).on("submit","#frmCadCliente",function(event){
	// Prevent form submission
    event.preventDefault();
    
    var dataForm = {
        IDCLIENTE    : $("#TXT_CUSTOMER_NEW_ID").val(),
        NOME         : $("#TXT_CUSTOMER_NEW_NAME").val(),
        DATA_CADASTRO: $("#TXT_CUSTOMER_NEW_DATEADD").val(),
        EMAIL        : $("#TXT_CUSTOMER_NEW_EMAIL").val(),
        NASCIMENTO   : $("#TXT_CUSTOMER_NEW_BIRTHDAY").val(),
        CPF          : $("#TXT_CUSTOMER_NEW_TAXVAT").val(),
        CEP          : $("#TXT_CUSTOMER_NEW_ZIPCODE").val(),
        GENERO       : $("#TXT_CUSTOMER_NEW_GENDER").val(),
        TELEFONE     : $("#TXT_CUSTOMER_NEW_PHONE").val(),
        TELEFONE2    : $("#TXT_CUSTOMER_NEW_PHONE2").val()
    };
    
    $.ajax({
       	headers:{
	   		'X-CSRF-Token':csrf
	   	},
       	method: 'post',
       url: BASE_URL_JS+'retail/customer_data_save/true',
       data: dataForm,
       dataType: 'json',
       success: function(data){
           if(data.IDCLIENTE>0){
                setCustomerData(data);
                $("#modalNewCustomer").modal('hide');
                
                $("#frmCadCliente").removeClass("was-validated");
                
                //limpa o cadastro
                $("#TXT_CUSTOMER_NEW_ID").val("");
                $("#TXT_CUSTOMER_NEW_NAME").val("");
                $("#TXT_CUSTOMER_NEW_EMAIL").val("");
                $("#TXT_CUSTOMER_NEW_BIRTHDAY").val("");
                $("#TXT_CUSTOMER_NEW_TAXVAT").val("");
                $("#TXT_CUSTOMER_NEW_ZIPCODE").val("");
                $("#TXT_CUSTOMER_NEW_GENDER").val("");
                $("#TXT_CUSTOMER_NEW_PHONE").val("");
                $("#TXT_CUSTOMER_NEW_PHONE2").val("");
           }
           else{
                if(data.IDCLIENTE=="-1"){
                    bootbox.alert(data.NOME);
                    $("#txtCadClienteCPF").focus();
                }else{
                    $("#modalNewCustomer").modal('hide');
                    //limpa o cadastro
                    $("#frmCadCliente").removeClass("was-validated");
                    $("#TXT_CUSTOMER_NEW_NAME").val("");
                    $("#TXT_CUSTOMER_NEW_EMAIL").val("");
                    $("#TXT_CUSTOMER_NEW_BIRTHDAY").val("");
                    $("#TXT_CUSTOMER_NEW_TAXVAT").val("");
                    $("#TXT_CUSTOMER_NEW_ZIPCODE").val("");
                    $("#TXT_CUSTOMER_NEW_GENDER").val("");
                    $("#TXT_CUSTOMER_NEW_PHONE").val("");
                    $("#TXT_CUSTOMER_NEW_PHONE2").val("");
                }
           }
       }
    });
});

/**
* Preenche as informacoes do cliente na tela de cadastro caso seja necessario editar
* @param {object} data dados clo cliente
* 
* @return null
*/
function setCustomerRegistry(data){
    var d = new Date(data.NASCIMENTO);
    $("#TXT_CUSTOMER_NEW_ID").val(data.IDCLIENTE);
    $("#TXT_CUSTOMER_NEW_BIRTHDAY").val(zeroFill(d.getDate(),2)+"/"+zeroFill((d.getMonth()+1),2)+"/"+d.getFullYear());
    $("#TXT_CUSTOMER_NEW_GENDER").val(data.GENERO);
    $("#TXT_CUSTOMER_NEW_TAXVAT").val(data.CPF);
    $("#TXT_CUSTOMER_NEW_ZIPCODE").val(data.CEP);
    $("#TXT_CUSTOMER_NEW_PHONE").val(data.TELEFONE);
    $("#TXT_CUSTOMER_NEW_PHONE2").val(data.TELEFONE2);
    $("#TXT_CUSTOMER_NEW_NAME").val(data.NOME);
    $("#TXT_CUSTOMER_NEW_EMAIL").val(data.EMAIL);
}

/**
* Evento que realiza a busca no formulario de busca de funcionario
*/
$(document).on("submit","#frmFindEmployer",function(event){
    event.preventDefault();

    if($("#txtCpfFuncionario").val()!="" || $("#txtNameFuncionario").val()!="" || $("#txtFoneFuncionario").val()!=""){
        var dataForm = {
            txtCpfFuncionario  : $("#txtCpfFuncionario").val(),
            txtNameFuncionario : $("#txtNameFuncionario").val(),
            txtFoneFuncionario : $("#txtFoneFuncionario").val()
        };

        $.ajax({
        	headers:{
				'X-CSRF-Token':csrf
			},
            method: 'post',
            url: '/hr/employer_dialog',
            data: dataForm,
            success: function(data){
                $("#tblFindEmployer tbody").html(data);
            }
        });
    }
    else{
        $("#tblFindEmployer tbody").html("");
    }
});

/**
* Evento executado na exibicao do formulario de busca de funcionarios
*/
$(document).on('shown.bs.modal',"#modalFindEmployer",function(){
    $("#txtNomeFuncionario").focus();
});

/**
* Evento executado ao fechar o formulario de busca de funcionarios
*/
$(document).on('hidden.bs.modal',"#modalFindEmployer",function(){
    $("#tblFindEmployer tbody").html("");
    $("#txtCpfFuncionario").val("");
    $("#txtNameFuncionario").val("");
    $("#txtFoneFuncionario").val("");
});

/**
* Evento de clique no botao btnUseEmployer, aqui serah chamada a funcao
* useEmployer que deve ser declarada na pagina
*/
$(document).on("click","#btnUseEmployer",function(){
    $("input[name='rdEmployer[]']").each(function(){
        if($(this)[0].checked){
            useEmployer($(this).val());
            $("#modalFindEmployer").modal('hide');
        }
    });
});