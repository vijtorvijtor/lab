#!/bin/bash
dira=$(pwd)
sh=$dira"/sube.ftp"
echo -e "\nSintasys: ./sube.sh {patron de archivos incluyendo directorio, uso comun en /home/vertice/opt/*php }\n"
patron=$1
dirRemoto="public_html/$2"
echo "Parametros: patron:[$patron], dirRemoto:[$dirRemoto]"
echo patron: [$patron]
echo "#!/bin/bash" >$sh
echo "HOST='opticaeco.com'">>$sh
echo "US='u947744031'" >>$sh
echo "PA='V3rt1ceV3rt1ce'" >>$sh
#cd $dirw


echo "ftp  -g -d -v -t  -i -p -v  -n  \$HOST <<EOF" >>$sh
echo "user \$US \$PA ">>$sh
echo "hash" >>$sh
echo "ascii" >>$sh
echo "cd public_html">>$sh
echo "cd $2">>$sh
echo "pwd" >>$sh
ls $patron
for f in $(ls $patron);  do echo "put $f $(basename $f)">>$sh ; done
echo "disconnect">>$sh
echo "close">>$sh
echo "bye">>$sh
echo "EOF" >>$sh
chmod +x $sh
cd $dira
#echo "contenido de $sh:-----"
#cat $sh
#echo "--------"
#echo Ahora ejecutara el sh...
. /$sh
