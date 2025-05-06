<style>
	.multifields {
		margin: 0 !important;
		padding: 0 !important;
	}

	.multifields div {
		margin: 10px 0;
	}

	.multifields label {
		margin-bottom: 5px;
	}
</style>

<?php

$file = $pathContent . $slug . '-settings.json';


$fileAll =  $pathContent . 'allmultifield-settings.json';
$fileinput = $pathContent . $slug . '.json';


$filer = '';


if (file_exists($file) || file_exists($fileAll)) {

	$filer .= @file_get_contents($file) ?? '';

	if (file_exists($file) && file_exists($fileAll)) {
		$filer = substr($filer, 0, -1) . ',';
		$filer .= substr(file_get_contents($fileAll), 1);
	};

	if (!file_exists($file) && file_exists($fileAll)) {
		$filer .= file_get_contents($fileAll);
	};
} else {
	$filer = '{}';
};




if (file_exists($fileinput)) {
	$fileData = file_get_contents($fileinput);
} else {
	$fileData = '{}';
};



?>


<?php

$t = new Pages();; ?>

<?php if (file_exists($file) || file_exists($fileAll)) : ?>

	<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

	<div x-data='{counters:0,dates:<?php echo $filer; ?>,dater:<?php echo $fileData; ?>}' x-init="document.querySelector('#jsform').append($el)" class="multifields" style="height:30vh;padding:20px;box-sizing:border-box">


		<template x-for="(data,index) in dates" :key="index">
			<div>



				<template x-if="data['type']=='wysywig'">
					<div>
						<label x-text="data['label'].replace(/-/g,' ')"></label>
						<input style="margin:5px 0;display:block;" type="hidden" :value="data['label']" name="label-multifield[]">
						<input style="margin:5px 0;display:block;" type="hidden" :value="data['type']" name="type-multifield[]">
						<textarea x-html="dater[data['label']]['value'] === undefined ? '' : dater[data['label']]['value'] " class="wysywigField" name="multifield[]"></textarea>
					</div>
				</template>


				<template x-if="data['type']=='textarea'">
					<div>
						<label x-text="data['label'].replace(/-/g,' ')"></label>
						<input style="margin:5px 0;display:block;" type="hidden" :value="data['label']" name="label-multifield[]">
						<input style="margin:5px 0;display:block;" type="hidden" :value="data['type']" name="type-multifield[]">
						<textarea x-html="dater[data['label']]['value'] === undefined ? '' : dater[data['label']]['value'] " class="form-control" name="multifield[]"></textarea>
					</div>
				</template>



				<template x-if=" data['type']=='text' || data['type']=='color' || data['type']=='date' || data['type']=='date'">
					<div>
						<label x-text="data['label'].replace(/-/g,' ')"></label>
						<input style="margin:5px 0;display:block;" type="hidden" :value="data['label']" name="label-multifield[]">
						<input style="margin:5px 0;display:block;" type="hidden" :value="data['type']" name="type-multifield[]">
						<input style="margin:5px 0;display:block;" :type="data['type']" class="form-control" :value="dater[data['label']]['value'] " name="multifield[]">
					</div>
				</template>



				<template x-if="data['type']=='dropdown'">
					<div>

						<label x-text="data['label'].replace(/-/g,' ').split('[')[0]"></label>
						<input style="margin:5px 0;display:block;" type="hidden" :value="data['label']" name="label-multifield[]">
						<input style="margin:5px 0;display:block;" type="hidden" :value="data['type']" name="type-multifield[]">

						<select name="multifield[]" style="background:#fff;border:solid 1px #ddd;width:100%;padding:10px;border-radius:5px;">

							<template x-for="option in data['label'].substring(data['label'].indexOf('[') + 1, data['label'].indexOf(']')).split(',')">
								<option x-text="option.replace(/-/g, ' ')" :value="option" :selected="dater[data['label']]['value'] == option"></option>
							</template>

						</select>
					</div>
				</template>




				<template x-if="data['type']=='foto'">
					<div>
						<label x-text="data['label'].replace(/-/g,' ')"></label>
						<input style="margin:5px 0;display:block;" type="hidden" :value="data['label']" name="label-multifield[]">
						<input class="form-control" type="hidden" :value="data['type']" name="type-multifield[]">

						<div style="display:flex;gap:2px">
							<input style=" width:91%" class="form-control" :type="data['type']" :value="dater[data['label']]['value'] === undefined ? '' : dater[data['label']]['value']" class="foto" name="multifield[]">
							<button class="btn btn-primary" @click.prevent="window.open(`${HTML_PATH_ADMIN_ROOT}/plugin/multiField?&browser&func=multifield[]&count=${index}&folder=<?php echo $t->db[$slug]['uuid']; ?>`,'myWindow','tolbar=no,scrollbars=no,menubar=no,width=500,height=500')">Get Photo</button>
						</div>
					</div>

				</template>


				<template x-if="data['type']=='checkbox'">
					<div>
						<label x-text="data['label'].replace(/-/g,' ')"></label>
						<input style="margin:5px 0;display:block;" type="hidden" :value="data['label']" name="label-multifield[]">
						<input style="margin:5px 0;display:block;" type="hidden" :value="data['type']" name="type-multifield[]">
						<input value="on" x-bind:checked="dater[data['label']]['value'] == 'on'" style="all:revert;" type="checkbox" name="multifield[]">
					</div>
				</template>



				<template x-if=" data['type']=='link'">
					<div>
						<label x-text="data['label'].replace(/-/g,' ')"></label>
						<input style="margin:5px 0;display:block;" type="hidden" :value="data['label']" name="label-multifield[]">
						<input style="margin:5px 0;display:block;" type="hidden" :value="data['type']" name="type-multifield[]">

						<select name="multifield[]" class="form-control">

							<?php foreach (glob(PATH_PAGES . '*', GLOB_ONLYDIR) as $file) {
								$new = new Pages();
								$filePure = pathinfo($file)['filename'];
								$url = DOMAIN_BASE . pathinfo($file)['filename'];
								$title = $new->db[$filePure]['title'];
 								echo '<option :selected="dater[data[`label`]][`value`] == `'.$url.'`" value="' . $url . '" >' . $title . '</option>';
							}; ?>

						</select>

					</div>
				</template>


		</template>
	</div>


	<script>
		tinymce.init({
			selector: '.wysywigField',
			element_format: "html",
			entity_encoding: "raw",
			skin: "oxide",
			schema: "html5",
			statusbar: false,
			menubar: false,
			branding: false,
			browser_spellcheck: true,
			pagebreak_separator: PAGE_BREAK,
			paste_as_text: true,
			remove_script_host: false,
			convert_urls: true,
			relative_urls: false,
			valid_elements: "*[*]",
			cache_suffix: "?version=5.10.5",

			plugins: ["code autolink image link pagebreak advlist lists textpattern table"],
			toolbar1: "formatselect bold italic forecolor backcolor removeformat | bullist numlist table | blockquote alignleft aligncenter alignright | link unlink pagebreak image code",
			toolbar2: "",
			language: "en",
			content_css: "<?php echo DOMAIN_BASE; ?>bl-plugins/tinymce/css/tinymce_content.css",
			codesample_languages: [],
		})
	</script>



<?php endif; ?>