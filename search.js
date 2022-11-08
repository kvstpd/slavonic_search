// Δόξα σοι ὁ Θεὸς ἡμῶν, δόξα σοι.
// Сла́ва тебѣ̀ бж҃е на́шъ, сла́ва тебѣ̀.
// Glory to Thee, our God, glory to Thee.

const csl_min_query_length = 1; // 1 for debug, 3+ for normal operation
const csl_url_prefix = window.location.href.slice(0, window.location.href.lastIndexOf('/') + 1);
//const csl_input_poll_interval = 1400;
//let csl_input_poller = undefined;
//let csl_last_search = '';


const csl_get_radio_value = (name, def_value) =>
{
	const radios = document.getElementsByName(name);
	let result = def_value;

	for (let i = 0, length = radios.length; i < length; i++) 
	{
		if (radios[i].checked) {
			result =radios[i].value;
			break;
		}
	}
	return result;
}

const csl_set_checkboxes_by_class = (class_name, check) =>
{
    const checkboxes = window.document.getElementsByClassName(class_name);
    
    for (let i = 0; i < checkboxes.length; i++) {
        checkboxes[i].checked = check;
    }
}


const csl_get_checkboxes_values_by_class = (class_name, check_value) =>
{
    const checkboxes = window.document.getElementsByClassName(class_name);
    const ids = [];
    
    for (let i = 0; i < checkboxes.length; i++) {
        if (checkboxes[i].checked)
            ids.push(checkboxes[i].value);
    }
    
    return ids.join(',');
}


const csl_load_json = (address, result_handler) =>
{
    const request = new XMLHttpRequest();
    
    request.onreadystatechange = () => {
            if (request.readyState == 4 && request.status == 200)
                result_handler(request.responseText);
        }
    
    request.open("GET", address, true);
    request.send(null);
}

const csl_clear_old_results = () =>
{
    window.document.getElementById('csl_occurences').innerHTML =  '';
    window.document.getElementById('csl_context').innerHTML =  '';
}


const csl_check_multi_word = (search) =>
{
    const multi_params = window.document.getElementById('csl_multi_words');
    const is_multi = search.includes(' ');
    
    //multi_params.disabled = !is_multi;
    
    multi_params.style.display = is_multi ? 'block' : 'none';
    
    return is_multi;
}

const csl_display_json = (result_json, area_id, entry_formatter) =>
{
    const area = window.document.getElementById(area_id);
    
    const data = JSON.parse(result_json);
    
    if (data['success'] == false)
    {
        area.innerHTML = 'ERROR';
        return;
    }
        
    let toPrint = '';
    
    for (let index = 0; index < data['result'].length; index++)
    {
        entry = data['result'][index];
        toPrint =  toPrint + entry_formatter(entry);
    }
        
    area.innerHTML = toPrint;
}


const csl_display_words = (result_json) =>
{
    //const formatter = (entry) => { return '<p class="slv" onclick="csl_query_for_context(\'' + entry.occurence_id +  '\')">' + entry.word + '</p>'; }
    const formatter = (entry) =>
    {
        return '<p class="slv" onclick="csl_query_for_occurences(\'' + entry.word_id +  '\')">' + entry.word + ' (' + entry.total + ')</p>';
    }
    csl_display_json(result_json, 'csl_search_result', formatter);
}

const csl_display_occurences = (result_json) =>
{
    const formatter = (entry) =>
    {
        return '<p class="occ" onclick="csl_query_for_context(\'' + entry.occurence_id +  '\')">'
            +  entry.ref + '</p>';
    }
    csl_display_json(result_json, 'csl_occurences', formatter);
}


const csl_display_context = (result_json) =>
{
	const par_class = 'slv_' + csl_get_radio_value('result_encoding', 'unicode');

    const formatter = (entry) => { return '<p class="' + par_class + '">' +  entry  + '</p>'; }
    csl_display_json(result_json, 'csl_context', formatter);
}

const csl_query_for_occurences = (word_id) =>
{
    csl_clear_old_results();
    const addr = csl_url_prefix + "occurences_json.php?word_id=" + word_id  + '&book_ids=' + csl_get_checkboxes_values_by_class('csl_book');
    csl_load_json(addr, csl_display_occurences);
}


const csl_query_for_context = (occurence_id) =>
{
    const addr = csl_url_prefix + "context_json.php?occurence_id=" + occurence_id + '&encoding=' + csl_get_radio_value('result_encoding', 'unicode');
    csl_load_json(addr, csl_display_context);
}





const csl_make_search = () =>
{
    const typed_value = window.document.getElementById('csl_search_box').value.trim();
	const match_type =  csl_get_radio_value('match_type', 'contains');
	const query_encoding =  csl_get_radio_value('encoding', 'simplified');

    if (typed_value.length < csl_min_query_length)
        return;

    //console.log(typed_value);
    //if (typed_value != csl_last_search)
    //{
        csl_clear_old_results();
        
        const is_multi = csl_check_multi_word(typed_value);
        
        const addr = csl_url_prefix + "search_json.php?search="
            + encodeURIComponent(typed_value)
            + "&match_type=" + match_type
            + "&encoding=" + query_encoding
            + '&book_ids=' + csl_get_checkboxes_values_by_class('csl_book')
            + (is_multi ?
               ('&multi_type=' + csl_get_radio_value('multi_words', 'multi_and_rigid')
                + '&multi_distance=' + window.document.getElementById('csl_word_distance').value )
               : '');
        
        csl_load_json(addr, csl_display_words);
		//csl_last_search = typed_value;
    //}
}


/*const csl_start_typing_query = (e) =>
{
    csl_input_poller = window.setInterval(csl_tick_while_typing, csl_input_poll_interval);
}

const csl_stop_typing_query = (e) =>
{
    window.clearInterval(csl_input_poller);
}*/

/*const csl_search_param_changed = (e) =>
{
    csl_last_search = '';
    csl_make_search();
}*/

const csl_book_type_selected = (e) =>
{
    const book_type =  csl_get_radio_value('book_type', 'c');
    
    if (book_type == '__choose')
    {
        window.document.getElementById('csl_book_list').style.display = 'inline-block';
        window.document.getElementById('csl_book_checkboxes').disabled = false;
    }
    else
    {
        // first uncheck everything if it's not 'all'
        if (book_type != 'csl_book')
            csl_set_checkboxes_by_class('csl_book', false);
        
        csl_set_checkboxes_by_class(book_type, true);
        
        window.document.getElementById('csl_book_checkboxes').disabled = true;
    }
    
    //csl_search_param_changed();
}


const csl_when_loaded = () =>
{
    //window.document.getElementById('csl_search_box').addEventListener('focus', csl_start_typing_query);
    //window.document.getElementById('csl_search_box').addEventListener('blur', csl_stop_typing_query);
    
    window.document.getElementById('csl_search_box').addEventListener("keypress", function(event) {
      if (event.key === "Enter")
          csl_make_search();
    });
    
    
    window.document.getElementById('csl_search_button').addEventListener('click', csl_make_search);
    
    //const search_param_fields = window.document.getElementsByClassName('csl_search_param');
    
    //for (let i = 0; i < search_param_fields.length; i++) {
     //   search_param_fields[i].addEventListener('click', csl_search_param_changed);
    //}
    
    const book_select_fields = window.document.getElementsByClassName('csl_book_select');
    
    for (let i = 0; i < book_select_fields.length; i++) {
        book_select_fields[i].addEventListener('click', csl_book_type_selected);
    }
}

window.addEventListener ("load", csl_when_loaded);
