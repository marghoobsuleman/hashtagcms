<style scoped>
    .action-link {
        cursor: pointer;
    }
</style>

<template>
    <div>
        <div class="card card-default">
            <div class="card-header">
                <div style="display: flex; justify-content: space-between; align-items: center;">
                    <h2>
                        OAuth Clients - <span class="badge badge-info">{{clients.length}}</span>
                    </h2>
                    <button tabindex="-1" title="Add New" type="button" aria-label="Add New" class="btn btn-default" @click="showCreateClientForm">
                        <i aria-hidden="true" class="fa fa-plus"></i> Create New Client
                    </button>
                </div>
            </div>

            <div class="card-body">
                <!-- Current Clients -->
                <p class="mb-0 text-warning" v-if="clients.length === 0">
                    You have not created any OAuth clients.
                </p>

                <table class="table table-borderless mb-0" v-if="clients.length > 0">
                    <thead>
                        <tr>
                            <th>Client ID</th>
                            <th>Name</th>
                            <th>Secret</th>
                            <th width="100" align="right"></th>
                        </tr>
                    </thead>

                    <tbody>
                        <tr v-for="client in clients">
                            <!-- ID -->
                            <td style="vertical-align: middle;">
                                <a class="action-link" tabindex="-1" @click="edit(client)">{{ client.id }}</a>
                            </td>

                            <!-- Name -->
                            <td style="vertical-align: middle;">
                                {{ client.name }}
                            </td>

                            <!-- Secret -->
                            <td style="vertical-align: middle;">
                                <code>{{ client.secret }}</code>
                            </td>


                            <!-- Edit/Delete Button -->
                            <td style="vertical-align: middle; padding-right: 10px" align="right">
                                <a class="action-link" tabindex="-1" @click="edit(client)">
                                    Edit
                                </a> &nbsp;
                                <a class="action-link text-danger" @click="destroy(client)">
                                    Delete
                                </a>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Create Client Modal -->
        <modal-box ref="modalCreateClient" data-show-footer="true">
            <div slot="title">
                Create Client
            </div>
            <div slot="content">
                <div class="alert alert-danger" v-if="createForm.errors.length > 0">
                    <p class="mb-0"><strong>Whoops!</strong> Something went wrong!</p>
                    <br>
                    <ul>
                        <li v-for="error in createForm.errors">
                            {{ error }}
                        </li>
                    </ul>
                </div>

                <!-- Create Client Form -->
                <form role="form">
                    <!-- Name -->
                    <div class="form-group row">
                        <label class="col-md-3 col-form-label">Name</label>

                        <div class="col-md-9">
                            <input id="create-client-name" type="text" class="form-control"
                                   @keyup.enter="store" v-model="createForm.name">

                            <span class="form-text text-muted">
                                        Something your users will recognize and trust.
                                    </span>
                        </div>
                    </div>

                    <!-- Redirect URL -->
                    <div class="form-group row">
                        <label class="col-md-3 col-form-label">Redirect URL</label>

                        <div class="col-md-9">
                            <input type="text" class="form-control" name="redirect"
                                   @keyup.enter="store" v-model="createForm.redirect">

                            <span class="form-text text-muted">
                                        Your application's authorization callback URL.
                                    </span>
                        </div>
                    </div>

                    <!-- Confidential -->
                    <div class="form-group row">
                        <label class="col-md-3 col-form-label">Confidential</label>

                        <div class="col-md-9">
                            <div class="checkbox">
                                <label>
                                    <input type="checkbox" v-model="createForm.confidential">
                                </label>
                            </div>

                            <span class="form-text text-muted">
                                        Require the client to authenticate with a secret. Confidential clients can hold credentials in a secure way without exposing them to unauthorized parties. Public applications, such as native desktop or JavaScript SPA applications, are unable to hold secrets securely.
                                    </span>
                        </div>
                    </div>
                </form>
            </div>
            <div slot="footer" class="center-align">
                <button type="button" class="btn btn-primary" @click="store">
                    Create
                </button>
            </div>
        </modal-box>

        <!-- Edit Client Modal -->

        <modal-box ref="modalEditClient" data-show-footer="true">
            <div slot="title">
                Edit Client
            </div>
            <div slot="content">

                <div class="alert alert-danger" v-if="editForm.errors.length > 0">
                    <p class="mb-0"><strong>Whoops!</strong> Something went wrong!</p>
                    <br>
                    <ul>
                        <li v-for="error in editForm.errors">
                            {{ error }}
                        </li>
                    </ul>
                </div>

                <!-- Edit Client Form -->
                <form role="form">
                    <!-- Name -->
                    <div class="form-group row">
                        <label class="col-md-3 col-form-label">Name</label>

                        <div class="col-md-9">
                            <input id="edit-client-name" type="text" class="form-control"
                                   @keyup.enter="update" v-model="editForm.name">

                            <span class="form-text text-muted">
                                        Something your users will recognize and trust.
                                    </span>
                        </div>
                    </div>

                    <!-- Redirect URL -->
                    <div class="form-group row">
                        <label class="col-md-3 col-form-label">Redirect URL</label>

                        <div class="col-md-9">
                            <input type="text" class="form-control" name="redirect"
                                   @keyup.enter="update" v-model="editForm.redirect">

                            <span class="form-text text-muted">
                                        Your application's authorization callback URL.
                                    </span>
                        </div>
                    </div>
                </form>
            </div>
            <div slot="footer" class="center-align">
                <button type="button" class="btn btn-primary" @click="update">
                    Save Changes
                </button>
            </div>
        </modal-box>

    </div>
</template>

<script>
    import {Toast, Modal, Loader} from '../../helpers/Common';

    export default {
        /*
         * The component's data.
         */
        data() {
            return {
                clients: [],

                createForm: {
                    errors: [],
                    name: '',
                    redirect: '',
                    confidential: true
                },

                editForm: {
                    errors: [],
                    name: '',
                    redirect: ''
                }
            };
        },

        /**
         * Prepare the component (Vue 1.x).
         */
        ready() {
            this.prepareComponent();
        },

        /**
         * Prepare the component (Vue 2.x).
         */
        mounted() {
           this.prepareComponent();
        },

        methods: {
            /**
             * Prepare the component.
             */
            prepareComponent() {
                this.getClients();
            },

            /**
             * Get all of the OAuth clients for the user.
             */
            getClients() {
                axios.get('/oauth/clients')
                        .then(response => {
                            this.clients = response.data;
                        });
            },

            /**
             * Show the form for creating new clients.
             */
            showCreateClientForm() {
                Modal.hide(this);
                Modal.open(this, "modalCreateClient");
            },

            /**
             * Create a new OAuth client for the user.
             */
            store() {
                this.persistClient(
                    'post', '/oauth/clients',
                    this.createForm, 'modalCreateClient'
                );
            },

            /**
             * Edit the given client.
             */
            edit(client) {
                //console.log(client);
                this.editForm.id = client.id;
                this.editForm.name = client.name;
                this.editForm.redirect = client.redirect;
                Modal.hide(this);
                Modal.open(this, "modalEditClient");
            },

            /**
             * Update the client being edited.
             */
            update() {
                this.persistClient(
                    'put', '/oauth/clients/' + this.editForm.id,
                    this.editForm, 'modalEditClient'
                );
            },

            /**
             * Persist the client to storage using the given form.
             */
            persistClient(method, uri, form, modal) {
                form.errors = [];
                var $this = this;
                axios[method](uri, form)
                    .then(response => {
                        this.getClients();

                        form.name = '';
                        form.redirect = '';
                        form.errors = [];

                        Modal.hide(this);
                    })
                    .catch(error => {
                        if (typeof error.response.data === 'object') {
                            form.errors = _.flatten(_.toArray(error.response.data.errors));
                        } else {
                            form.errors = ['Something went wrong. Please try again.'];
                        }
                    });
            },

            /**
             * Destroy the given client.
             */
            destroy(client) {
                axios.delete('/oauth/clients/' + client.id)
                        .then(response => {
                            this.getClients();
                        });
            }
        }
    }
</script>
