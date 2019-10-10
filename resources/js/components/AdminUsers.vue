<template>
    <div id="usuarios">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">Usuarios</h4>
                        <div class="card-tools alignRight">
                            <button type="button" class="btn btn-info" @click="create" title="Adicionar usuario"><i
                                    class="fas fa-plus"></i>
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <div v-if="usuarios.length == 0 && !searched"class="row justify-content-center">
                            <td colspan="6">
                                <div class="alert text-center" role="alert">
                                    Aun no existen usuarios registrados al sistema.
                                </div>
                            </td>
                        </div>
                        <div v-else>
                        <div class="mb-3">
                            <div class="row">
                                <div class="col-md-2 mb-1">
                                    <strong>Buscar por: </strong>
                                </div>
                                <div class="col-md-3 mb-1">
                                    <select v-model="queryField" id="fields" class="form-control" @change="searchData">
                                        <option value="username" selected>Usuario</option>
                                        <option value="name">Nombre</option>
                                        <option value="email">Correo</option>
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
                                    <th scope="col">Usuario</th>
                                    <th scope="col">Nombre</th>
                                    <th scope="col">Correo</th>

                                    <th scope="col" class="text-center">Acci&oacute;n</th>
                                </tr>
                                </thead>
                                <tbody>
                                <tr v-show="usuarios.length" v-for="(usuario, index) in usuarios" :key="usuario.id">
                                    <!--                                    <th scope="row">{{index+1}}</th>-->
                                    <td>{{usuario.username}}</td>
                                    <td>{{usuario.name}}</td>
                                    <td>{{usuario.email}}</td>
                                    <td class="text-center">
                                        <button type="button" class="btn btn-info btn-sm">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        <button type="button" @click="edit(usuario)" class="btn btn-primary btn-sm" title="Adicionar usuario">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button type="button" @click="destroy(usuario)" class="btn btn-danger btn-sm">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                    </td>
                                </tr>

                                <tr v-if="usuarios.length == 0 && searched">
                                    <td colspan="6">
                                        <div class="alert text-center" role="alert">
                                            Lo sentimos: (No se encontraron datos asociados a la b&uacute;squeda.)
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
                                    @paginate="query === ''? getUsuarios():searchData()"
                        ></pagination>
                    </div>
                </div>
            </div>
            <vue-progress-bar></vue-progress-bar>
            <vue-snotify></vue-snotify>

        </div>

        <div class="overflow-auto:scroll"></div>
        <!--Modal-->
        <div class="modal fade" id="usuarioModalLong" tabindex="-1" role="dialog"
             aria-labelledby="usuarioModalLongTitle" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="usuarioModalLongTitle">
                            {{editMode ? 'Editar usuario':'Adicionar usuario'}}</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <form @submit.prevent="editMode ? update() : store()" @keydown="form.onKeydown($event)">
                        <div class="modal-body">
                            <!--                            <alert-error :form="form"></alert-error>-->
                            <div class="form-group">
                                <label>Nombre</label>
                                <input v-model="form.name" type="text" name="name"
                                       class="form-control" :class="{ 'is-invalid': form.errors.has('name') }" >
                                <has-error :form="form" field="name"></has-error>
                            </div>
                            <div class="form-group">
                                <label>Usuario</label>
                                <input v-model="form.username" type="text" name="username"
                                       class="form-control" :class="{ 'is-invalid': form.errors.has('username') }">
                                <has-error :form="form" field="username"></has-error>
                            </div>
                            <div class="form-group">
                                <label>Correo</label>
                                <input v-model="form.email" type="text" name="email"
                                       class="form-control" :class="{ 'is-invalid': form.errors.has('email') }">
                                <has-error :form="form" field="email"></has-error>
                            </div>

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
        <!--EndModal-->
    </div>

</template>

<script>

    export default {
        data() {
            return {
                editMode: false,
                query: '',
                queryField: 'username',
                searched: '',
                editable: false,
                usuarios: [],
                usuarioAeditar: {
                    name: '',
                    username: '',
                    email: ''
                },
                form: new Form({
                    id: '',
                    name: '',
                    username: '',
                    email: '',
                    busy: false
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
                    this.getUsuarios()
                } else {
                    this.searchData()
                }
            }
        },
        created() {
        },
        mounted() {
            this.getUsuarios()
        },
        computed: {
            fullData() {
                if (this.editMode) {
                    if (this.form.name === '' || this.form.username === '' || this.form.email === '')
                        return true
                    if (this.usuarioAeditar.name !== this.form.name || this.usuarioAeditar.username !== this.form.username || this.usuarioAeditar.email !== this.form.email)
                        return false
                    return true
                } else {
                    if (this.form.name === '' || this.form.username === '' || this.form.email === '')
                        return true
                    return false
                }
            }
        },
        methods: {
            setFocus: function()
            {
                // Note, you need to add a ref="search" attribute to your input.
                this.$refs.namefocus.focus();
            },
            getUsuarios: function () {
                let urlUsuarios = '/api/usuarios?page=' + this.pagination.current_page
                this.$Progress.start()
                axios.get(urlUsuarios).then(response => {
                    this.usuarios = response.data.usuarios.data
                    this.pagination = response.data.usuarios
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
                    axios.get('/api/search/usuarios/' + this.queryField + '/' + this.query + '?page=' + this.pagination.current_page)
                        .then(response => {
                            this.usuarios = response.data.data
                            this.pagination = response.data
                            this.searched = 'true'
                            this.$Progress.finish()
                        })
                        .catch(e => {
                            console.log("ERROR" + e)
                            this.$Progress.fail()
                        })
                }


            },
            reload() {
                this.getUsuarios()
                this.query = ''
                this.queryField = 'username'
                this.searched = ''
                this.$snotify.success('Pagina Cargada satisfactoriamente')

            },
            create() {
                this.form.busy = true
                this.editMode = false
                this.form.reset()
                this.form.clear
                $('#usuarioModalLong').modal('show');
                },
            store() {
                this.$Progress.start()
                this.form.busy = true
                this.form.post('/api/usuarios').then(response => {
                    this.getUsuarios()
                    if (this.form.successful) {
                        this.form.reset()
                        this.form.clear()
                        this.$Progress.finish()
                        this.$snotify.success('Usuario Adicionado Satisfactoriamente')
                    } else {
                        this.$Progress.fail()
                        this.$snotify.error('Hay algun error. Compruebe al administrador de red')
                    }

                })
                    .catch(e => {
                        this.$Progress.fail()
                        this.$snotify.error('El usuario existe o necesita introducir algun valor')
                    })

            },
            edit(usuario) {
                this.editMode = true
                this.form.reset()
                this.form.clear()
                this.form.fill(usuario)
                this.usuarioAeditar.name = this.form.name
                this.usuarioAeditar.username = this.form.username
                this.usuarioAeditar.email = this.form.email
                $('#usuarioModalLong').modal('show');
            },
            update() {
                this.$Progress.start()
                this.form.busy = true
                this.form.put('/api/usuarios/' + this.form.id).then(response => {
                    this.getUsuarios()
                    $('#usuarioModalLong').modal('hide')
                    if (this.form.successful) {

                        this.$Progress.finish()
                        this.$snotify.success('Usuario editado Satisfactoriamente')
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
            destroy(usuario) {
                this.$snotify.clear
                this.$snotify.confirm(
                    "No volverás a recuperar los datos del usuario!!",
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
                                    axios.delete('/api/usuarios/' + usuario.id)
                                        .then(response => {
                                            console.log(this.pagination.data.length)
                                            if (this.pagination.data.length == 1) {
                                                this.pagination.current_page = this.pagination.last_page - 1
                                                console.log(this.pagination.current_page)
                                            }
                                            this.getUsuarios()
                                            this.$Progress.finish()
                                            this.$snotify.success("Usuario eliminado satisfactoriamente",
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

        }
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
