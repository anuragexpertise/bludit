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

	.file-upload-container {
		display: flex;
		gap: 10px;
		align-items: center;
		margin-top: 5px;
	}

	.file-upload-container input[type="text"] {
		flex-grow: 1;
	}

	.current-media-preview {
		margin-top: 10px;
	}

	.current-media-preview img,
	.current-media-preview video {
		max-width: 200px;
		max-height: 200px;
		display: block;
	}

	.upload-progress {
		display: none;
		margin-top: 5px;
		color: #666;
		font-size: 12px;
	}

	.upload-error {
		color: #d9534f;
		margin-top: 5px;
		font-size: 12px;
	}
</style>

<?php

$file = $pathContent . $slug . '-settings.json';


$fileAll = $pathContent . 'allmultifield-settings.json';
$fileinput = $pathContent . $slug . '.json';


$filer = '';


if (file_exists($file) || file_exists($fileAll)) {

	$filer .= @file_get_contents($file) ?? '';

	if (file_exists($file) && file_exists($fileAll)) {
		$filer = substr($filer, 0, -1) . ',';
		$filer .= substr(file_get_contents($fileAll), 1);
	}
	;

	if (!file_exists($file) && file_exists($fileAll)) {
		$filer .= file_get_contents($fileAll);
	}
	;
} else {
	$filer = '{}';
}
;




if (file_exists($fileinput)) {
	$fileData = file_get_contents($fileinput);
} else {
	$fileData = '{}';
}
;



?>


<?php

$t = new Pages();
; ?>

<?php if (file_exists($file) || file_exists($fileAll)): ?>

	<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

	<div x-data='{counters:0,dates:<?php echo $filer; ?>,dater:<?php echo $fileData; ?>}'
		x-init="document.querySelector('#jsform').append($el)" class="multifields"
		style="height:30vh;padding:20px;box-sizing:border-box">


		<template x-for="(data,index) in dates" :key="index">
			<div>



				<template x-if="data['type']=='wysywig'">
					<div>
						<label x-text="data['label'].replace(/-/g,' ')"></label>
						<input style="margin:5px 0;display:block;" type="hidden" :value="data['label']"
							name="label-multiFields[]">
						<input style="margin:5px 0;display:block;" type="hidden" :value="data['type']"
							name="type-multiFields[]">
						<textarea x-html="dater[data['label']]['value'] === undefined ? '' : dater[data['label']]['value'] "
							class="wysywigField" name="multiFields[]"></textarea>
					</div>
				</template>


				<template x-if="data['type']=='textarea'">
					<div>
						<label x-text="data['label'].replace(/-/g,' ')"></label>
						<input style="margin:5px 0;display:block;" type="hidden" :value="data['label']"
							name="label-multiFields[]">
						<input style="margin:5px 0;display:block;" type="hidden" :value="data['type']"
							name="type-multiFields[]">
						<textarea x-html="dater[data['label']]['value'] === undefined ? '' : dater[data['label']]['value'] "
							class="form-control" name="multiFields[]"></textarea>
					</div>
				</template>



				<template
					x-if=" data['type']=='text' || data['type']=='color' || data['type']=='date' || data['type']=='date'">
					<div>
						<label x-text="data['label'].replace(/-/g,' ')"></label>
						<input style="margin:5px 0;display:block;" type="hidden" :value="data['label']"
							name="label-multiFields[]">
						<input style="margin:5px 0;display:block;" type="hidden" :value="data['type']"
							name="type-multiFields[]">
						<input style="margin:5px 0;display:block;" :type="data['type']" class="form-control"
							:value="dater[data['label']]['value'] " name="multiFields[]">
					</div>
				</template>



				<template x-if="data['type']=='dropdown'">
					<div>

						<label x-text="data['label'].replace(/-/g,' ').split('[')[0]"></label>
						<input style="margin:5px 0;display:block;" type="hidden" :value="data['label']"
							name="label-multiFields[]">
						<input style="margin:5px 0;display:block;" type="hidden" :value="data['type']"
							name="type-multiFields[]">

						<select name="multiFields[]"
							style="background:#fff;border:solid 1px #ddd;width:100%;padding:10px;border-radius:5px;">

							<template
								x-for="option in data['label'].substring(data['label'].indexOf('[') + 1, data['label'].indexOf(']')).split(',')">
								<option x-text="option.replace(/-/g, ' ')" :value="option"
									:selected="dater[data['label']]['value'] == option"></option>
							</template>

						</select>
					</div>
				</template>


				<template x-if="data['type']=='checkbox'">
					<div>
						<label x-text="data['label'].replace(/-/g,' ')"></label>
						<input style="margin:5px 0;display:block;" type="hidden" :value="data['label']"
							name="label-multiFields[]">
						<input style="margin:5px 0;display:block;" type="hidden" :value="data['type']"
							name="type-multiFields[]">
						<input value="on" x-bind:checked="dater[data['label']]['value'] == 'on'" style="all:revert;"
							type="checkbox" name="multiFields[]">
					</div>
				</template>

				<template x-if=" data['type']=='link'">
					<div>
						<label x-text="data['label'].replace(/-/g,' ')"></label>
						<input style="margin:5px 0;display:block;" type="hidden" :value="data['label']"
							name="label-multiFields[]">
						<input style="margin:5px 0;display:block;" type="hidden" :value="data['type']"
							name="type-multiFields[]">

						<select name="multiFields[]" class="form-control">

							<?php foreach (glob(PATH_PAGES . '*', GLOB_ONLYDIR) as $file) {
								$new = new Pages();
								$filePure = pathinfo($file)['filename'];
								$url = DOMAIN_BASE . pathinfo($file)['filename'];
								$title = $new->db[$filePure]['title'];
								echo '<option :selected="dater[data[`label`]][`value`] == `' . $url . '`" value="' . $url . '" >' . $title . '</option>';
							}
							; ?>

						</select>

					</div>
				</template>

				<template x-if="data['type']=='foto'">
					<div>
						<label x-text="data['label'].replace(/-/g,' ')"></label>
						<input type="hidden" :value="data['label']" name="label-multiFields[]">
						<input type="hidden" :value="data['type']" name="type-multiFields[]">

						<div class="file-upload-container">
							<input type="text" class="form-control"
								:value="dater[data['label']]['value'] === undefined ? '' : dater[data['label']]['value']"
								name="multiFields[]" readonly>
							<input type="file" class="d-none" accept="image/*"
								@change="handleFileUpload($event, $el.closest('div').querySelector('input[name=\"
								multiFields[]\"]'), 'image' )">
							<button class="btn btn-primary" type="button"
								@click="$event.target.previousElementSibling.click()">
								Get Photo
							</button>
						</div>
						<div class="upload-progress" x-ref="imageProgress"></div>
						<div class="upload-error" x-ref="imageError"></div>

						<template x-if="dater[data['label']] && dater[data['label']]['value']">
							<div class="current-media-preview">
								<img :src="dater[data['label']]['value']" :alt="data['label']">
								<label class="glassy-checkbox">
									<input type="checkbox" name="delete-image[]" :value="data['label']">
									Remove image
								</label>
							</div>
						</template>
					</div>
				</template>

				<template x-if="data['type']=='video'">
					<div>
						<label x-text="data['label'].replace(/-/g,' ')"></label>
						<input type="hidden" :value="data['label']" name="label-multiFields[]">
						<input type="hidden" :value="data['type']" name="type-multiFields[]">

						<div class="file-upload-container">
							<input type="text" class="form-control"
								:value="dater[data['label']]['value'] === undefined ? '' : dater[data['label']]['value']"
								name="multiFields[]" readonly>
							<input type="file" class="d-none" accept="video/*"
								@change="handleFileUpload($event, $el.closest('div').querySelector('input[name=\"
								multiFields[]\"]'), 'video' )">
							<button class="btn btn-primary" type="button"
								@click="$event.target.previousElementSibling.click()">
								Get Video
							</button>
						</div>
						<div class="upload-progress" x-ref="videoProgress"></div>
						<div class="upload-error" x-ref="videoError"></div>

						<template x-if="dater[data['label']] && dater[data['label']]['value']">
							<div class="current-media-preview">
								<video controls width="200">
									<source :src="dater[data['label']]['value']"
										:type="'video/' + dater[data['label']]['value'].split('.').pop()">
								</video>
								<label class="glassy-checkbox">
									<input type="checkbox" name="delete-video[]" :value="data['label']">
									Remove video
								</label>
							</div>
						</template>
					</div>
				</template>

		</template>
	</div>


	<!-- <script>
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
	</script> -->
	<script>
		async function handleAjaxUpload(event, targetInput, type) {
			const file = event.target.files[0];
			if (!file) return;

			const progressElement = event.target.closest('div').nextElementSibling;
			const errorElement = progressElement.nextElementSibling;

			// Reset previous messages
			progressElement.style.display = 'block';
			progressElement.textContent = 'Uploading...';
			errorElement.style.display = 'none';
			errorElement.textContent = '';

			const formData = new FormData();
			formData.append('tokenCSRF', document.getElementById('tokenCSRF').value);
			formData.append(type === 'video' ? 'multiFields-video' : 'image', file);

			try {
				const response = await fetch(`${HTML_PATH_ADMIN_ROOT}plugin/multiFields`, {
					method: 'POST',
					body: formData,
					headers: {
						'X-Requested-With': 'XMLHttpRequest'
					}
				});

				if (!response.ok) {
					throw new Error(`HTTP error! status: ${response.status}`);
				}

				const data = await response.json();

				if (!data.success) {
					throw new Error(data.error || 'Upload failed');
				}

				// Update the input field
				targetInput.value = data.path;

				// Trigger Alpine.js to update the preview
				const alpineComponent = Alpine.closestRoot(event.target);
				if (alpineComponent) {
					alpineComponent.$data.dater[targetInput.getAttribute('data-label')] = {
						value: data.path,
						type: type
					};
				}

				// Update preview
				const previewContainer = targetInput.closest('.file-upload-container').nextElementSibling;
				if (previewContainer && previewContainer.classList.contains('current-media-preview')) {
					if (type === 'image') {
						let img = previewContainer.querySelector('img');
						if (!img) {
							img = document.createElement('img');
							previewContainer.prepend(img);
						}
						img.src = data.path;
						img.alt = targetInput.getAttribute('data-label');
					} else if (type === 'video') {
						let video = previewContainer.querySelector('video');
						if (!video) {
							video = document.createElement('video');
							video.controls = true;
							video.width = 200;
							const source = document.createElement('source');
							video.appendChild(source);
							previewContainer.prepend(video);
						}
						video.querySelector('source').src = data.path;
						video.querySelector('source').type = `video/${data.path.split('.').pop()}`;
						video.load();
					}
				}

				progressElement.textContent = 'Upload complete!';
				setTimeout(() => {
					progressElement.style.display = 'none';
				}, 2000);

			} catch (error) {
				console.error('Upload error:', error);
				errorElement.style.display = 'block';
				errorElement.textContent = `Error: ${error.message}`;
				progressElement.style.display = 'none';
			}
		}
	</script>
<?php endif; ?>