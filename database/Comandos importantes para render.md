# Presiona Ctrl+C para cancelar el proceso actual
# Luego ejecuta solo el seeder de usuarios:
docker-compose exec app php artisan db:seed --class=QuickAccessUsersSeeder

# Verificar usuarios en la base de datos:
docker-compose exec app php artisan tinker
# Luego en tinker:
User::all()

Crear una nueva rama para ese módulo
Desde tu proyecto local:
    git checkout -b feature/Nombre-Modulo
Esto crea y te mueve a la nueva rama

Cuando esté listo para enviar a producción
Primero, asegúrate de estar en tu rama de desarrollo:
    git branch

Luego cambia a main:
    git checkout main

Actualiza tu main con lo último del servidor:
    git pull

Ahora sí mezcla los cambios:
git merge feature/finanzas

4️⃣ Sube los cambios a Render
    git push

📌 Render detectará el push y hará deploy automático ✅

5️⃣ Opcional: borrar la rama (si ya no la necesitas)
    git branch -d feature/finanzas