# Usa una imagen base oficial de Python
FROM python:3.9

# Establece el directorio de trabajo dentro del contenedor
WORKDIR /app

# Copia el archivo requirements.txt al contenedor
COPY requirements.txt .

# Instala las dependencias listadas en requirements.txt
RUN pip install --no-cache-dir -r requirements.txt

# Copia todo el código de la app al contenedor
COPY . .

# Expone el puerto en el que correrá Flask (ajusta si usas otro)
EXPOSE 5000

# Comando para ejecutar la app Flask
CMD ["python", "app.py"]

