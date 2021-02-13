<!--
  - ODM.Web  https://github.com/electropsycho/ODM.Web
  - Copyright 2019-2020 Hakan GÜLEN
  -
  - Licensed under the Apache License, Version 2.0 (the "License");
  - you may not use this file except in compliance with the License.
  - You may obtain a copy of the License at
  -
  - http://www.apache.org/licenses/LICENSE-2.0
  -
  - Unless required by applicable law or agreed to in writing, software
  - distributed under the License is distributed on an "AS IS" BASIS,
  - WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
  - See the License for the specific language governing permissions and
  - limitations under the License.
  -->

<template>
  <section class="content">
    <div class="row">
      <div class="col-md-12">
        <div class="box">
          <div class="box-header with-border">
            <h4>Yeni Soru Oluşturma</h4>
          </div>
          <div class="box-body">
            <div class="row">
              <div class="col-md-offset-2 col-md-8">
                <div class="panel panel-default clearfix">
                  <div class="panel-heading">
                    <h3 class="panel-title">
                      Soru
                    </h3>
                  </div>
                  <div class="panel-body">
                    <div
                      class="form-group has-feedback"
                      :class="{'has-error': errors.has('branch')}"
                    >
                      <label>Ders/Alan Seçimi</label>
                      <v-select
                        v-model="branch"
                        v-validate="'required'"
                        :options="branches"
                        :reduce="b => b.id"
                        label="name"
                        name="branch"
                        placeholder="Alan/Ders adının en az 3 harfini girin"
                        @search="searchBranches"
                      >
                        <div slot="no-options">
                          Burada bişey bulamadık :-(
                        </div>
                      </v-select>
                      <span
                        v-if="errors.has('branch')"
                        class="error invalid-feedback"
                      >{{ errors.first('branch') }}</span>
                    <!--          <input v-model="branch_id" type="text" class="form-control" placeholder="Branş Seçimi">-->
                    <!--          <span class="glyphicon glyphicon-barcode form-control-feedback"></span>-->
                    </div>
                    <div
                      class="form-group has-feedback"
                      :class="{'has-error': errors.has('selectedClassLevel')}"
                    >
                      <label>Sınıf Seviyesi Seçimi</label>
                      <v-select
                        v-model="selectedClassLevel"
                        v-validate="'required'"
                        :options="classLevels"
                        name="selectedClassLevel"
                        placeholder="Sınıf seviyesini seçiniz"
                      />
                      <span
                        v-if="errors.has('selectedClassLevel')"
                        class="error invalid-feedback"
                      >{{ errors.first('selectedClassLevel') }}</span>
                      <!--          <input v-model="branch_id" type="text" class="form-control" placeholder="Branş Seçimi">-->
                      <!--          <span class="glyphicon glyphicon-barcode form-control-feedback"></span>-->
                    </div>
                    <div
                      v-if="branch != null && selectedClassLevel != null"
                      class="form-group has-feedback"
                      :class="{'has-error': errors.has('learningOutCome')}"
                    >
                      <label>Kazanım Seçimi</label>
                      <v-select
                        v-model="learningOutCome"
                        v-validate="'required'"
                        :options="learningOutcomes"
                        :reduce="lo => lo.id"
                        label="content"
                        name="learningOutCome"
                        placeholder="Kazanım içeriğinden veya kodundan en az 3 harf giriniz"
                        @search="searchLearningOutcomes"
                      >
                        <div slot="no-options">
                          Burada bişey bulamadık :-(
                        </div>
                      </v-select>
                      <span
                        v-if="errors.has('learningOutCome')"
                        class="error invalid-feedback"
                      >{{ errors.first('learningOutCome') }}</span>
                    <!--          <input v-model="branch_id" type="text" class="form-control" placeholder="Branş Seçimi">-->
                    <!--          <span class="glyphicon glyphicon-barcode form-control-feedback"></span>-->
                    </div>
                    <div class="form-group has-feedback">
                      <label>Soru Öncülü Üst (Varsa)</label>
                      <ckeditor
                        v-model="header"
                        :editor="editor"
                        :config="editorConfig"
                      />
                    </div>
                    <div
                      class="form-group has-feedback"
                      :class="{'has-error': errors.has('questionFile')}"
                    >
                      <label>Grafik Dosyası(Varsa)</label>
                      <input
                        v-validate="'image|size:1000'"
                        name="questionFile"
                        type="file"
                        @change="selectQuestionGraphic($event)"
                      >
                      <span
                        v-if="errors.has('questionFile')"
                        class="error invalid-feedback"
                      >{{ errors.first('questionFile') }}</span>
                    </div>
                    <div class="form-group has-feedback">
                      <label>Soru Öncülü Alt (Varsa)</label>
                      <ckeditor
                        v-model="footer"
                        :editor="editor"
                        :config="editorConfig"
                      />
                    </div>
                    <div
                      class="form-group has-feedback"
                      :class="{'has-error': errors.has('itemRoot')}"
                    >
                      <label>Soru Kökü</label>
                      <ckeditor
                        v-model="itemRoot"
                        v-validate="'required'"
                        name="itemRoot"
                        :editor="editor"
                        :config="editorConfig"
                      />
                      <span
                        v-if="errors.has('itemRoot')"
                        class="error invalid-feedback"
                      >{{ errors.first('itemRoot') }}</span>
                    </div>
                  </div>
                </div>
                <div class="panel panel-default">
                  <div class="panel-heading clearfix">
                    <h3
                      class="panel-title pull-left"
                    >
                      Seçenekler
                    </h3>
                    <div class="btn-group pull-right">
                      <button
                        :class="{ disabled : choices.length >= 5 }"
                        class="btn btn-mini btn-success btn-block btn-flat"
                        @click="addChoice"
                      >
                        Ekle
                      </button>
                    </div>
                  </div>
                  <div class="panel-body">
                    <div
                      v-for="(choice, index) in choices"
                      :key="index"
                      class="row"
                    >
                      <div class="col-md-12">
                        <div class="row">
                          <div class="col-md-12">
                            <div class="form-group has-feedback">
                              <label>Seçenek {{ index + 1 }}</label>
                              <ckeditor
                                v-model="choice.content"
                                :editor="editor"
                                :config="editorConfig"
                                @input="atLeastOneImageOrContentMustBeAddedToTheOptions"
                              />
                            </div>
                          </div>
                        </div>
                        <div class="row">
                          <div class="col-md-12">
                            <div class="col-md-4">
                              <p-check
                                v-model="choice.isCorrect"
                                class="p-switch p-fill"
                                color="success"
                                @change="correctChoiceChanged(index)"
                              >
                                Doğru cevap
                              </p-check>
                            </div>
                            <div class="col-md-4">
                              <div
                                class="form-group has-feedback"
                                :class="{'has-error': errors.has('choiceFile')}"
                              >
                                <input
                                  v-validate="'image|size:1000'"
                                  name="choiceFile"
                                  type="file"
                                  class="pull-left"
                                  @change="selectChoiceGraphic($event, index)"
                                >
                                <span
                                  v-if="errors.has('choiceFile')"
                                  class="error invalid-feedback"
                                >{{ errors.first('choiceFile') }}</span>
                              </div>
                            </div>
                            <div class="col-md-4">
                              <div class="btn-group pull-right">
                                <button
                                  class="btn btn-mini btn-danger btn-block btn-flat"
                                  @click="removeChoice(index)"
                                >
                                  Sil
                                </button>
                              </div>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                    <div
                      v-if="choices != null && choices.length < 4"
                      class="row"
                    >
                      <div class="col-md-12">
                        <div class="form-group has-error">
                          <label
                            class="error invalid-feedback"
                          >* Seçenek sayısı 4(dört)'ten az olamaz!</label>
                        </div>
                      </div>
                    </div>
                    <div
                      v-if="isThereACorrectAnswer"
                      class="row"
                    >
                      <div class="col-md-12">
                        <div class="form-group has-error">
                          <label
                            class="error invalid-feedback"
                          >* Seçenekler biri doğru yanıt olmalıdır!</label>
                        </div>
                      </div>
                    </div>
                    <div
                      v-if="showError3"
                      class="row"
                    >
                      <div class="col-md-12">
                        <div class="form-group has-error">
                          <label
                            class="error invalid-feedback"
                          >* Seçenekler en az 1(bir) resim veya içeriğe sahip olmalıdır!</label>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <div class="row">
              <div class="col-md-offset-4 col-md-4">
                <button
                  class="btn btn-success btn btn-block"
                  :class="{'disabled': errors.any() || showError3 || isThereACorrectAnswer || isSending }"
                  @click="save"
                >
                  {{ isSending ? '%'+ progressVal: 'Kaydet' }}
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>
</template>

<script>
// import InlineEditor from 'ckeditor5-build-classic-with-alignment'
// import CkEditor from '@ckeditor/ckeditor5-vue'
// import vSelect from 'vue-select'
// import QuestionService from '../../services/QuestionService'
// import Messenger from '../../helpers/messenger'
// import { MessengerConstants } from '../../helpers/constants'

export default {
  name: 'NewQuestion',
  // eslint-disable-next-line vue/no-unused-components
  // components: {
  //   ckeditor: CkEditor.component,
  //   vSelect
  // },
  data () {
    return {
      branches: [],
      branch: null,
      learningOutcomes: [],

      header: null,
      footer: null,
      itemRoot: null,

      files: [],
      questionFileName: null,
      questionFile: null,
      choices: [],

      selectedClassLevel: null,
      classLevels: [...Array(9).keys()].map(i => i + 4),

      learningOutCome: null,
      learningOutComes: [],

      // En az bir resim veya içerik ekleme hataıs kontorlü
      showError3: true,

      isSending: false,
      progressVal: 0,

      // editor: InlineEditor,
      editorConfig: {
        // The configuration of the editor.
        language: 'tr',
        toolbar: ['heading', '|',
          'alignment', // <--- ADDED
          'bold',
          'italic',
          'insertTable',
          'bulletedList',
          'numberedList',
          'undo',
          'redo']
      }
    }
  },
  computed: {
    isThereACorrectAnswer () {
      // En az bir doğru yanıt olmalıdır.
      return !(this.choices.filter(value => value.isCorrect).length >= 1)
    }
  },
  // watch: {
  //   choices () {
  //     this.atLeastOneImageOrContentMustBeAddedToTheOptions()
  //   },
  //   files () {
  //     this.atLeastOneImageOrContentMustBeAddedToTheOptions()
  //   }
  // },
  methods: {
    atLeastOneImageOrContentMustBeAddedToTheOptions () {
      const res = this.choices.filter(value => {
        if (value.content.match('<p>&nbsp;</p>')) {
          value.content = ''
        }
        const c = !!value.content
        const f = !!value.file
        return c || f
      }).length < 4
      this.showError3 = res
    },
    addChoice () {
      // 5'ten fazla seçenek eklememinin önüne geçyoruz
      if (this.choices.length < 5) {
        this.choices.push(
          {
            file: null,
            fileName: '',
            content: '',
            isCorrect: false
          }
        )
      }
      this.atLeastOneImageOrContentMustBeAddedToTheOptions()
    },
    removeChoice (index) {
      // Seçenek silme
      this.choices.splice(index, 1)
      // Seçeneğe bağlı dosyayı immutable tarzda siliyoruz
      // this.files = this.files.filter(val => val.index !== index)
      this.atLeastOneImageOrContentMustBeAddedToTheOptions()
    },
    correctChoiceChanged (index) {
      // Tek bir doğru cevap olabilir. Kontrol ediyoruz.
      this.choices.forEach((value, index1) => { if (index1 !== index) value.isCorrect = false })
    },
    selectChoiceGraphic (event, index) {
      // Her seçenek için tek bir dosya seçilebilir.
      // Hali hazırda seçenek için seçilmiş bir dosya varsa onu silip yeniyi ekliyoruz
      if (event.target.files[0]) {
        this.choices[index].file = event.target.files[0]
        this.choices[index].fileName = event.target.files[0].name
      } else {
        this.choices[index].file = null
        this.choices[index].fileName = null
      }
      this.atLeastOneImageOrContentMustBeAddedToTheOptions()
    },
    selectQuestionGraphic (event) {
      this.questionFile = event.target.files[0]
    },
    searchBranches (search, loading) {
      // TODO: Burada refactoring yapılcak servis içine alınacak bu kod parçası
      if (search.length >= 3) {
        this.$http.get('/auth/branches', {
          params: {
            searchTerm: search
          }
        })
          .then(result => {
            // console.log(result)
            this.branches = result.data
          })
          .catch(res => console.log(res))
      }
    },
    searchLearningOutcomes (search, loading) {
      // TODO: Burada refactoring yapılcak servis içine alınacak bu kod parçası
      if (search.length < 3) { return }
      const data = {
        class_level: this.selectedClassLevel,
        content: search,
        lesson_id: this.branch
      }
      this.$http.post('/learning_outcomes/find_by/content_lesson_id_class_level', data, {
        headers: {
          'Content-Type': 'application/json'
        }
      })
        .then(response => {
          this.learningOutcomes = response.data
        })
    },
    save () {
      this.$validator.validateAll().then(valRes => {
        if (valRes) {
          this.isSending = true
          const fd = new FormData()
          fd.append('lesson_id', this.branch)
          fd.append('learning_outcome_id', this.learningOutCome)
          fd.append('header', this.header)
          fd.append('footer', this.footer)
          fd.append('item_root', this.itemRoot)
          if (this.questionFile) {
            fd.append('question_file', this.questionFile, this.questionFile.name)
          }

          this.choices.forEach((c, i) => {
            fd.append(`choices[${i}][content]`, c.content)
            fd.append(`choices[${i}][correct]`, c.isCorrect)
            if (c.file) {
              fd.append(`choices[${i}][file]`, c.file, c.file.name)
            }
          })
          // QuestionService.save(fd, (progress) => {
          //   this.progressVal = progress
          //   console.log(progress)
          // }).then(resp => {
          //   // Messenger.showSuccess(resp.message)
          //   this.isSending = false
          // }).catch(e => {
          //   this.isSending = false
          //   // Messenger.showError(MessengerConstants.errorMessage)
          // })
        }
      })
        .catch(e => {
          this.isSending = false
        })
      this.isSending = false
    }
  }
}
</script>

<style lang="sass">

</style>
