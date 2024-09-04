<template>
    <div class="inline-block relative">
        <button
            class="bard-toolbar-button"
            v-html="button.html"
            v-tooltip="button.text"
            @click="toggleDropdown"
        ></button>

        <div class="absolute popover-content p-4 bg-white dark:bg-dark-550 shadow-popover dark:shadow-dark-popover rounded-md"
             v-if="showDropdown" v-click-outside="toggleDropdown">
            <div class="block mb-2">
                <button @click="send" class="btn-primary">Correct spelling</button>
                <p class="text-xs text-black dark:text-white mt-2">{{ status }}</p>
            </div>
        </div>
    </div>
</template>
<script>
import vClickOutside from 'v-click-outside'

export default {
    name: 'Spell Corrector',
    directives: {
        clickOutside: vClickOutside.directive
    },
    mixins: [BardToolbarButton],
    data() {
        return {
            showDropdown: false,
            promptText: '',
            type: 'full',
            charactersReceived: 0,
            status: '',
        };
    },
    methods: {
        toggleDropdown() {
            this.showDropdown = !this.showDropdown;
        },
        async send() {

            this.editor.setEditable(false);
            this.charactersReceived = 0.

            try {
                this.status = 'Correcting (0 chars)';

                const response = await fetch('/cp/vigilant/correct-spelling', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': StatamicConfig.csrfToken,
                    },
                    body: JSON.stringify({
                        bard: this.editor.getJSON()
                    })
                });

                if (!response.ok) {
                    Statamic.$toast.error(response?.data?.message || __('Something went wrong.'), {
                        duration: 10000
                    });
                    this.editor.commands.focus();
                    this.editor.setEditable(true);
                    this.status = 'Error';
                    return;
                }

                const reader = response.body.getReader();
                const decoder = new TextDecoder();
                let done = false;
                let newContent = '';

                while (!done) {
                    const {value, done: readerDone} = await reader.read();
                    done = readerDone;
                    if (value) {
                        const chunk = decoder.decode(value, {stream: true});

                        this.charactersReceived += chunk.length;
                        newContent += chunk;
                        this.status = 'Correcting (' + this.charactersReceived + ' chars)';
                    }
                }

                const json = newContent.match(/```json(.*?)```/s)[1];
                const doc = new DOMParser().parseFromString(json, "text/html");
                const decoded = doc.documentElement.textContent;

                let jsonArray;
                try {
                    jsonArray = JSON.parse(decoded);

                    this.editor.commands.setContent(jsonArray);

                    this.status = 'Finished! Check result in the editor.';
                } catch (error) {
                    Statamic.$toast.error(__('Failed to parse response'), {
                        duration: 10000
                    });

                    console.error("Error parsing JSON:", error);
                }

            } catch (error) {
                Statamic.$toast.error(error?.response?.data?.message || error.message || __('Something went wrong.'), {
                    duration: 10000
                });
                this.status = 'Error';
            } finally {
                this.editor.commands.focus();
                this.editor.setEditable(true);
            }
        }
    },
}

</script>

