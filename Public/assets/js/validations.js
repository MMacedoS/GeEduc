// Example starter JavaScript for disabling form submissions if there are invalid fields
$(document).ready(function() {
	$('#phone').on('input', function() {
	    var telefone = $(this).val();
	    var regex = /^\(?([0-9]{2})\)?[-. ]?([0-9]{1})[-. ]?([0-9]{4,5})[-. ]?([0-9]{4})$/;
	  
		if (!regex.test(telefone)) {
		 	$(this).addClass('is-invalid');
			$(this).siblings('.invalid-feedback').text('Telefone inválido');
			return;
		} 
		
		$(this).removeClass('is-invalid');
		$(this).siblings('.invalid-feedback').text('');
				  
		// Adiciona o formato de telefone
		var formattedPhone = telefone.replace(/^(\d{2})(\d{1})(\d{4,5})(\d{4})$/, '($1) $2 $3-$4');
		$(this).val(formattedPhone);
	});

	$('#type_doc').on('change', function() {
		var type_doc = $(this).val();
		if (type_doc == '') {
		  $(this).addClass('is-invalid');
		  $('#type_doc_error').text('Selecione um tipo de documento');
		  return;
		} 
		$(this).removeClass('is-invalid');
		$('#type_doc_error').text('');
		return;
	  });
  
	  $('#doc').on('input', function() {
		var doc = $(this).val();
		var type_doc = $('#type_doc').val();
		if (type_doc == 'CPF') {
		  var regex = /^\d{11}$/;
		  if (!regex.test(doc)) {
			$(this).addClass('is-invalid');
			$('#doc_error').text('CPF inválido');
			return;
		  } 
		  
		  $(this).removeClass('is-invalid');
		  $('#doc_error').text('');

		  var formattedDoc = doc.replace(/^(\d{3})(\d{3})(\d{3})(\d{2})$/, '$1.$2.$3-$4');
		  $(this).val(formattedDoc);
		  return;
		} 
		if (type_doc == 'CNH') {
		  var regex = /^\d{11}$/;
		  if (!regex.test(doc)) {
			$(this).addClass('is-invalid');
			$('#doc_error').text('CNH inválido');
			return;
		  } 
		 
		  $(this).removeClass('is-invalid');
			$('#doc_error').text('');

		  var formattedDoc = doc.replace(/^(\d{3})(\d{3})(\d{3})(\d{2})$/, '$1 . $2 .$3-$4');
		  $(this).val(formattedDoc);
		  return;
		} 
		
		if (type_doc == 'RG') {
		  var regex = /^\d{10}$/;
		  if (!regex.test(doc)) {
			$(this).addClass('is-invalid');
			$('#doc_error').text('RG inválido');
			return;
		  } 
		  
		  $(this).removeClass('is-invalid');
		  $('#doc_error').text('');
		  var formattedDoc = doc.replace(/^(\d{2})(\d{3})(\d{3})(\d{2})$/, '$1 . $2 .$3-$4');
		  $(this).val(formattedDoc);
		  return;
		
		}
		
		if (type_doc == 'PASSAPORTE') {
		  var regex = /^[A-Z]{2}\d{6}$/;
		  if (!regex.test(doc)) {
			$(this).addClass('is-invalid');
			$('#doc_error').text('Passaporte inválido');
			return;
		  } 
		  
		  $(this).removeClass('is-invalid');
		  $('#doc_error').text('');
		  return
		}
	  });

	$('#cnpj_company').on('input', function() {
	    var cnpj = $(this).val();
	    var regex = /^\d{14}$/;
	  
		if (!regex.test(cnpj)) {
		 	$(this).addClass('is-invalid');
			$(this).siblings('.invalid-feedback').text('cnpj inválido');
			return;
		} 
		
		$(this).removeClass('is-invalid');
		$(this).siblings('.invalid-feedback').text('');
				  
		// Adiciona o formato de cnpj
		var formattedCnpj = cnpj.replace(/^(\d{2})(\d{3})(\d{3})(\d{4})(\d{2})$/, '$1 $2 $3/$4-$5');
		$(this).val(formattedCnpj);
	});

	$('#phone_company').on('input', function() {
	    var telefone = $(this).val();
	    var regex = /^\(?([0-9]{2})\)?[-. ]?([0-9]{1})[-. ]?([0-9]{4,5})[-. ]?([0-9]{4})$/;
	  
		if (!regex.test(telefone)) {
		 	$(this).addClass('is-invalid');
			$(this).siblings('.invalid-feedback').text('Telefone inválido');
			return;
		} 
		
		$(this).removeClass('is-invalid');
		$(this).siblings('.invalid-feedback').text('');
				  
		// Adiciona o formato de telefone
		var formattedPhone = telefone.replace(/^(\d{2})(\d{1})(\d{4,5})(\d{4})$/, '($1) $2 $3-$4');
		$(this).val(formattedPhone);
	});

	$('#customers').select2();

	  const bankMapping = {
		"001": "Banco do Brasil",
		"104": "Caixa Econômica Federal",
		"237": "Bradesco",
		"341": "Itaú",
		"033": "Santander"
	  };
  
	  $("#bankCode").on("change", function () {
		const selectedCode = $(this).val();
		const correspondingBank = bankMapping[selectedCode] || "";
		$("#bankName").val(correspondingBank);
	  });
  
	  $("#bankName").on("change", function () {
		const selectedBank = $(this).val();
		const correspondingCode = Object.keys(bankMapping).find(key => bankMapping[key] === selectedBank) || "";
		$("#bankCode").val(correspondingCode);
	  });
  });