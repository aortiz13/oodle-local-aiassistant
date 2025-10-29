# Guía de Depuración - AI Assistant Plugin

## Problema: El input se bloquea después de enviar un mensaje

### Solución 1: Purgar los cachés de Moodle

Moodle cachea agresivamente los archivos JavaScript. Después de hacer cambios en el código, **DEBES** purgar los cachés:

1. **Opción A - Desde la interfaz de administración:**
   - Ir a: `Site administration > Development > Purge all caches`
   - Hacer clic en "Purge all caches"
   - Recargar la página en el navegador (Ctrl+F5 o Cmd+Shift+R)

2. **Opción B - Desde la línea de comandos:**
   ```bash
   php admin/cli/purge_caches.php
   ```

3. **Opción C - Activar modo de desarrollo (recomendado para testing):**
   - Ir a: `Site administration > Development > Debugging`
   - Cambiar "Debug messages" a "DEVELOPER"
   - Marcar "Display debug messages"
   - Esto desactiva el caché de JavaScript y CSS

### Solución 2: Verificar que los archivos estén actualizados

Asegúrate de que el archivo minificado esté sincronizado con el código fuente:

```bash
# Verificar que ambos archivos tengan ajax.call([{ con corchetes
grep "ajax.call" amd/src/chat.js
grep "ajax.call" amd/build/chat.min.js
```

Ambos deben mostrar `ajax.call([{` (con corchetes).

### Solución 3: Limpiar caché del navegador

1. Abrir las herramientas de desarrollo (F12)
2. Ir a la pestaña "Network"
3. Hacer clic derecho y seleccionar "Clear browser cache"
4. O hacer un hard reload: Ctrl+Shift+R (Windows/Linux) o Cmd+Shift+R (Mac)

### Solución 4: Verificar en la consola del navegador

1. Abrir herramientas de desarrollo (F12)
2. Ir a la pestaña "Console"
3. Enviar un mensaje en el chat
4. Buscar errores en rojo
5. Si ves errores como "ajax.call is not a function" o similares, reportarlos

## Cambios realizados para resolver el problema

### 1. Corregido ajax.call() en chat.js (líneas 79 y 117)
**Antes:**
```javascript
ajax.call({
    methodname: 'local_aiassistant_query',
    ...
});
```

**Después:**
```javascript
ajax.call([{
    methodname: 'local_aiassistant_query',
    ...
}]);
```

**Razón:** Moodle's ajax.call() requiere un array de objetos, no un objeto simple. Sin los corchetes, el callback `always` nunca se ejecuta, dejando el input permanentemente deshabilitado.

### 2. Agregados estilos CSS para inputs deshabilitados
```css
#ai-chat-input:disabled {
    background-color: #f5f5f5;
    cursor: not-allowed;
    opacity: 0.6;
}
```

Esto hace visualmente claro cuándo el input está deshabilitado.

## Cómo verificar que la corrección funciona

1. Purgar todos los cachés (ver arriba)
2. Recargar la página con Ctrl+Shift+R
3. Abrir el chat
4. Enviar un mensaje
5. **Verificar:**
   - El indicador "Typing..." aparece
   - La respuesta llega
   - El indicador "Typing..." desaparece
   - El input vuelve a estar habilitado (fondo blanco, cursor normal)
   - Puedes escribir otro mensaje

## Si el problema persiste

Contacta al desarrollador con la siguiente información:

1. Versión de Moodle
2. Errores en la consola del navegador (F12 > Console)
3. Captura de pantalla del problema
4. Resultado de: `php admin/cli/purge_caches.php`
