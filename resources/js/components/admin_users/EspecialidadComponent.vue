<template>
    <div id="usuarios">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div v-if="empty || fulled" class="card">
                    <div class="card-header">
                        <h4 class="card-title">Especialidades</h4>
                        <div class="card-tools alignRight">
                            <button type="button" class="btn btn-info" @click="create" title="Adicionar especialidad"><i
                                    class="fas fa-plus"></i>
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <div v-if="empty" class="row justify-content-center">
                            <td colspan="6">
                                <div class="alert text-center" role="alert">
                                    Aun no existen especialidades registradas al sistema.
                                </div>
                            </td>
                        </div>
                        <div v-if="fulled">
                            <div class="mb-3">
                                <div class="row">
                                    <div class="col-md-2 mb-1">
                                        <strong>Buscar por: </strong>
                                    </div>
                                    <div class="col-md-3 mb-1">
                                        <select v-model="queryField" id="fields" class="form-control"
                                                @change="searchData">
                                            <option value="nombre" selected>Nombre</option>
                                            <option value="sigla">Sigla</option>
                                        </select>
                                    </div>
                                    <div class="input-group col-md-7 mb-1">
                                        <input v-model="query" type="search" class="form-control" placeholder="Buscar">
                                    </div>
                                </div>
                            </div>
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                    <tr>
                                        <!--                                    <th scope="col">#</th>-->
                                        <th scope="col">Nombre</th>
                                        <th scope="col">Sigla</th>
                                        <th scope="col" class="text-center">Acci&oacute;n</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <tr v-for="(especialidad, index) in especialidades" :key="especialidad.id">
                                        <!--                                    <th scope="row">{{index+1}}</th>-->
                                        <td>{{especialidad.nombre}}</td>
                                        <td>{{especialidad.sigla}}</td>
                                        <td class="text-center">
                                            <button type="button" @click="edit(especialidad)"
                                                    class="btn btn-primary btn-sm"
                                                    title="Editar especialidad">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <button type="button" @click="destroy(especialidad)"
                                                    class="btn btn-danger btn-sm" title="Eliminar especialidad">
                                                <i class="fas fa-trash-alt"></i>
                                            </button>
                                        </td>
                                    </tr>

                                    <tr v-if="especialidades.length == 0 && searched">
                                        <td colspan="6">
                                            <div class="alert text-center text-danger" role="alert">
                                                <b class="errorlist">Ning&uacute;n elemento coincide con el criterio de
                                                    búsqueda.</b>
                                            </div>
                                        </td>
                                    </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <pagination v-if="pagination.last_page > 1"
                                    :pagination="pagination"
                                    :offset="5"
                                    @paginate="query === ''? getEspecialidades():searchData()"
                        ></pagination>
                    </div>
                </div>
            </div>
            <vue-progress-bar></vue-progress-bar>
            <vue-snotify></vue-snotify>

        </div>

        <div class="overflow-auto:scroll"></div>
        <!--Modal-->
        <div class="modal fade" id="especialidadModalLong" tabindex="-1" role="dialog"
             aria-labelledby="especialidadModalLongTitle" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 v-if="!viewMode" class="modal-title" id="especialidadModalLongTitle">
                            {{editMode ? 'Editando especialidad '+form.nombre:'Adicionar especialidad'}}</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div v-if="storeMode">
                        <form @submit.prevent="store()" @keydown="form.onKeydown($event)">
                            <div class="modal-body">
                                <!--                            <alert-error :form="form"></alert-error>-->
                                <div class="form-group">
                                    <label>Nombre</label>
                                    <input v-model="form.nombre" type="text" name="nombre"
                                           class="form-control" :class="{ 'is-invalid': form.errors.has('nombre') }">
                                    <has-error :form="form" field="nombre"></has-error>
                                </div>
                                <div class="form-group">
                                    <label>Sigla</label>
                                    <input v-model="form.sigla" type="text" name="sigla"
                                           class="form-control" :class="{ 'is-invalid': form.errors.has('sigla') }">
                                    <has-error :form="form" field="sigla"></has-error>
                                </div>
                                <div class="form-group">
                                    <label>Categor&iacute;a de pago</label>
                                    <select v-model="form.cat_pago_id" class="form-control">
                                        <!--                                        <option value="" disabled>Seleccione...</option>-->
                                        <option v-for="cat_pago in cat_pago_id" v-bind:value="cat_pago.id">
                                            {{cat_pago.nombre}}
                                        </option>
                                    </select>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Salir</button>
                                    <button :disabled="fullData" type="submit" class="btn btn-primary">Guardar
                                    </button>
                                </div>
                            </div>

                        </form>
                    </div>
                    <div v-if="editMode">
                        <form @submit.prevent="update()" @keydown="form.onKeydown($event)">
                            <div class="modal-body">
                                <div class="form-group">
                                    <label>Nombre</label>
                                    <input v-model="form.nombre" type="text" name="nombre"
                                           class="form-control" :class="{ 'is-invalid': form.errors.has('nombre') }">
                                    <has-error :form="form" field="nombre"></has-error>
                                </div>
                                <div class="form-group">
                                    <label>Sigla</label>
                                    <input v-model="form.sigla" type="text" name="sigla"
                                           class="form-control" :class="{ 'is-invalid': form.errors.has('sigla') }">
                                    <has-error :form="form" field="sigla"></has-error>
                                </div>

                                <label>Categor&iacute;a de pago</label>
                                <select v-model="form.cat_pago_id" class="form-control" >
                                    <option v-for="cat_pago in cat_pago_id" :value="cat_pago.id">
                                        {{cat_pago.nombre}}
                                    </option>
                                </select>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Salir</button>
                                    <button :disabled="fullData" type="submit" class="btn btn-primary">Guardar
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>

                </div>

            </div>
        </div>

        <!--EndModal-->
    </div>

</template>

<script>

    export default {
        data() {
            return {
                storeMode: false,
                editMode: false,
                viewMode: false,
                query: '',
                queryField: 'nombre',
                searched: '',
                empty: false,
                fulled: false,
                especialidades: [],
                cat_pago_id: [],
                especialidadAeditar: {
                    nombre: '',
                    sigla: '',
                    cat_pago_id:''
                },
                form: new Form({
                    id: '',
                    nombre: '',
                    sigla: '',
                    cat_pago_id:1,
                }),
                pagination: {
                    current_page: 1,
                },
                showModal: false

            }
        },
        watch: {
            query: function (newQ, old) {
                if (newQ === '') {
                    this.getEspecialidades()
                } else {
                    this.searchData()
                }
            }
        },
        created() {
        },
        mounted() {
            this.getCat_Pagos()
            this.getEspecialidades()
        },
        computed: {
            fullData() {
                if (this.editMode) {
                    if (this.especialidadAeditar.nombre === this.form.nombre && this.especialidadAeditar.sigla === this.form.sigla && this.especialidadAeditar.cat_pago_id === this.form.cat_pago_id) {
                        return true
                    } else {
                        return false
                    }

                }
                if (this.form.nombre === '' || this.form.sigla === '' || this.form.cat_pago_id === '')
                    return true

                return false
            }
        },
        methods: {
            setFocus: function () {
                // Note, you need to add a ref="search" attribute to your input.
                this.$refs.namefocus.focus();
            },
            getCat_Pagos() {
                axios.get('cat_pagos')
                    .then(response => {
                        this.cat_pago_id = response.data
                    })
            },
            getEspecialidades: function () {
                let urlEspecialidades = 'especialidades?page=' + this.pagination.current_page
                this.$Progress.start()
                axios.get(urlEspecialidades).then(response => {
                    console.log(response.data.cat_pago)
                    this.especialidades = response.data.especialidades.data
                    this.empty = this.especialidades.length == 0 ? true : false
                    this.fulled = !this.empty
                    this.pagination = response.data.especialidades
                    this.searched = ''
                    this.$Progress.finish()
                })
                    .catch(e => {
                        console.log(e)
                        this.$Progress.fail()
                    })
            },
            searchData() {
                if (this.query !== '') {
                    this.$Progress.start()
                    axios.get('search/especialidades/' + this.queryField + '/' + this.query + '?page=' + this.pagination.current_page)
                        .then(response => {
                            this.especialidades = response.data.data
                            this.pagination = response.data
                            this.searched = 'true'
                            this.$Progress.finish()
                        })
                        .catch(e => {
                            this.$Progress.fail()
                        })
                }


            },
            reload() {
                this.getEspecialidades()
                this.query = ''
                this.queryField = 'username'
                this.searched = ''
                this.$snotify.success('Pagina Cargada satisfactoriamente')

            },
            create() {
                this.form.busy = true
                this.storeMode = true
                this.editMode = false
                this.viewMode = false
                this.form.reset()
                this.form.clear
                $('#especialidadModalLong').modal('show');
            },
            store() {
                this.$Progress.start()
                this.form.busy = true
                this.form.post('especialidades')
                    .then(response => {
                        console.log(response)
                        this.getEspecialidades()
                        if (this.form.successful) {
                            this.form.reset()
                            this.form.clear()
                            this.$Progress.finish()
                            this.$snotify.success('Especialidad Adicionada Satisfactoriamente')
                        } else {
                            this.$Progress.fail()
                            this.$snotify.error('Existe algun error. Compruebe al administrador de red')
                        }

                    })
                    .catch(e => {
                        this.$Progress.fail()
                        this.$snotify.error('La especialidad ya existe en el sistema o ha ocurrido algun problema. Consulte al Administrador')
                    })

            },
            edit(especialidad) {
                this.storeMode = false
                this.form.reset()
                this.form.clear()
                this.form.fill(especialidad)
                this.editMode = true
                this.viewMode = false
                this.especialidadAeditar.nombre = especialidad.nombre
                this.especialidadAeditar.sigla = especialidad.sigla
                this.especialidadAeditar.cat_pago_id = especialidad.cat_pago_id
                $('#especialidadModalLong').modal('show');
            },
            update() {
                this.$Progress.start()
                this.form.busy = true
                this.form.put('especialidades/' + this.form.id).then(response => {
                    this.getEspecialidades()
                    $('#especialidadModalLong').modal('hide')
                    if (this.form.successful) {
                        this.$Progress.finish()
                        this.$snotify.success('Especialidad editada Satisfactoriamente')
                    } else {
                        this.$Progress.fail()
                        this.$snotify.error('Hay algun error. Compruebe al administrador de red')
                    }

                })
                    .catch(e => {
                        this.$Progress.fail()
                        this.$snotify.error('El usuario existe o necesita editar algun valor')
                    })
            },
            destroy(especialidad) {
                this.$snotify.clear
                this.$snotify.confirm(
                    "No volverás a recuperar los datos de la especialidad!!",
                    "Estás seguro?",
                    {
                        showProgressBar: false,
                        closeOnClick: false,
                        pauseOnHover: true,
                        buttons: [
                            {
                                text: "Sí",
                                action: toast => {
                                    this.$snotify.remove(toast.id)
                                    this.$Progress.start()
                                    axios.delete('especialidades/' + especialidad.id)
                                        .then(response => {
                                            if (this.pagination.data.length == 1) {
                                                this.pagination.current_page = this.pagination.last_page - 1
                                            }
                                            this.getEspecialidades()
                                            this.$Progress.finish()
                                            this.$snotify.success("Especialidad eliminada satisfactoriamente",
                                                "Correcto"
                                            )
                                        })
                                },
                                bold: true
                            },
                            {
                                text: "No",
                                action: toast => {
                                    this.$snotify.remove(toast.id)
                                },
                                bold: true
                            }
                        ]
                    }
                )
            }

        },


    }
</script>

<style scoped>
    .modal-mask {
        position: fixed;
        z-index: 9998;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0, 0, 0, .5);
        display: table;
        transition: opacity .3s ease;
    }

    .errorlist {
        color: grey;
    }

    .modal-wrapper {
        display: table-cell;
        vertical-align: middle;
    }

    .alignRight {
        position: absolute;
        right: 1rem;
        top: .5rem;
    }
</style>

