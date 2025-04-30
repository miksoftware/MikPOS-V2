<?php

    namespace App\Policies;

    use App\Models\User;
    use App\Models\Sucursal;
    use Illuminate\Auth\Access\HandlesAuthorization;

    class SucursalPolicy
    {
        use HandlesAuthorization;

        /**
         * Determine whether the user can view any models.
         */
        public function viewAny(User $user): bool
        {
            return $user->can('view_any_sucursal');
        }

        /**
         * Determine whether the user can view the model.
         */
        public function view(User $user, Sucursal $sucursal): bool
        {
            // Super Admin puede ver todas las sucursales
            if ($user->is_super_admin || $user->hasRole('Super Admin')) {
                return $user->can('view_sucursal');
            }

            // Usuarios normales solo pueden ver su sucursal asignada
            return $user->can('view_sucursal') && $user->sucursal_id === $sucursal->id;
        }

        /**
         * Determine whether the user can create models.
         */
        public function create(User $user): bool
        {
            return $user->can('create_sucursal');
        }

        /**
         * Determine whether the user can update the model.
         */
        public function update(User $user, Sucursal $sucursal): bool
        {
            // Super Admin puede actualizar todas las sucursales
            if ($user->is_super_admin || $user->hasRole('Super Admin')) {
                return $user->can('update_sucursal');
            }

            // Usuarios normales solo pueden actualizar su sucursal
            return $user->can('update_sucursal') && $user->sucursal_id === $sucursal->id;
        }

        /**
         * Determine whether the user can delete the model.
         */
        public function delete(User $user, Sucursal $sucursal): bool
        {
            // Super Admin puede eliminar todas las sucursales
            if ($user->is_super_admin || $user->hasRole('Super Admin')) {
                return $user->can('delete_sucursal');
            }

            // Usuarios normales solo pueden eliminar su sucursal
            return $user->can('delete_sucursal') && $user->sucursal_id === $sucursal->id;
        }

        /**
         * Determine whether the user can bulk delete.
         */
        public function deleteAny(User $user): bool
        {
            // Solo Super Admin puede eliminar varias sucursales
            if ($user->is_super_admin || $user->hasRole('Super Admin')) {
                return $user->can('delete_any_sucursal');
            }

            // Los usuarios regulares no pueden usar esta acción
            return false;
        }

        /**
         * Determine whether the user can permanently delete.
         */
        public function forceDelete(User $user, Sucursal $sucursal): bool
        {
            // Super Admin puede eliminar permanentemente todas las sucursales
            if ($user->is_super_admin || $user->hasRole('Super Admin')) {
                return $user->can('force_delete_sucursal');
            }

            // Usuarios normales solo pueden eliminar permanentemente su sucursal
            return $user->can('force_delete_sucursal') && $user->sucursal_id === $sucursal->id;
        }

        /**
         * Determine whether the user can permanently bulk delete.
         */
        public function forceDeleteAny(User $user): bool
        {
            // Solo Super Admin puede eliminar permanentemente varias sucursales
            if ($user->is_super_admin || $user->hasRole('Super Admin')) {
                return $user->can('force_delete_any_sucursal');
            }

            // Los usuarios regulares no pueden usar esta acción
            return false;
        }

        /**
         * Determine whether the user can restore.
         */
        public function restore(User $user, Sucursal $sucursal): bool
        {
            // Super Admin puede restaurar todas las sucursales
            if ($user->is_super_admin || $user->hasRole('Super Admin')) {
                return $user->can('restore_sucursal');
            }

            // Usuarios normales solo pueden restaurar su sucursal
            return $user->can('restore_sucursal') && $user->sucursal_id === $sucursal->id;
        }

        /**
         * Determine whether the user can bulk restore.
         */
        public function restoreAny(User $user): bool
        {
            // Solo Super Admin puede restaurar varias sucursales
            if ($user->is_super_admin || $user->hasRole('Super Admin')) {
                return $user->can('restore_any_sucursal');
            }

            // Los usuarios regulares no pueden usar esta acción
            return false;
        }

        /**
         * Determine whether the user can replicate.
         */
        public function replicate(User $user, Sucursal $sucursal): bool
        {
            // Super Admin puede replicar todas las sucursales
            if ($user->is_super_admin || $user->hasRole('Super Admin')) {
                return $user->can('replicate_sucursal');
            }

            // Usuarios normales solo pueden replicar su sucursal
            return $user->can('replicate_sucursal') && $user->sucursal_id === $sucursal->id;
        }

        /**
         * Determine whether the user can reorder.
         */
        public function reorder(User $user): bool
        {
            return $user->can('reorder_sucursal');
        }
    }
