import numpy.linalg
import csv
import glob


def getMatrix(path):
	matrix=[]
	fr=1
	with open(path, 'rb') as csvfile:
	    spamreader = csv.reader(csvfile, delimiter=',')
	    for row in spamreader:
	    	if fr:
	    		fr=0
	    	else:
	    		row.pop(0)
	    		matrix.append(row)

	mint=[]
	for line in matrix:
		mint.append(map(int, line))

	return mint

ev={}
fichiers = glob.glob('../analyze/transitions/*.csv')    #On liste uniquement les fichiers avec l'extension '.avi'
for fichier in fichiers:
	ev[fichier] = numpy.linalg.eigvals(getMatrix(fichier))

cmpEV=numpy.linalg.eigvals(getMatrix('/tmp/transition.csv'))

for author in ev:
	print author
	print numpy.vdot(ev[author], cmpEV)
